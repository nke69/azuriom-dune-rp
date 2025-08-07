<?php

namespace Azuriom\Plugin\DuneRp\Controllers\Admin;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Plugin\DuneRp\Models\House;
use Azuriom\Plugin\DuneRp\Models\SpiceTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminSpiceController extends Controller
{
    /**
     * Display the spice economy management dashboard.
     */
    public function index()
    {
        $economyStats = [
            'total_spice' => House::where('is_active', true)->sum('spice_reserves'),
            'total_transactions' => SpiceTransaction::count(),
            'average_reserves' => House::where('is_active', true)->avg('spice_reserves'),
            'richest_house' => House::where('is_active', true)
                                  ->orderByDesc('spice_reserves')
                                  ->first(),
            'daily_transactions' => SpiceTransaction::whereDate('created_at', today())->count(),
            'weekly_income' => SpiceTransaction::where('type', 'income')
                                             ->where('created_at', '>=', now()->subWeek())
                                             ->sum('amount'),
            'weekly_expenses' => SpiceTransaction::where('type', 'expense')
                                               ->where('created_at', '>=', now()->subWeek())
                                               ->sum('amount'),
            'monthly_net' => SpiceTransaction::where('created_at', '>=', now()->startOfMonth())
                                           ->selectRaw('
                                               SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) - 
                                               SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) as net
                                           ')
                                           ->value('net') ?? 0,
        ];

        $topHouses = House::where('is_active', true)
                         ->orderByDesc('spice_reserves')
                         ->limit(10)
                         ->get();

        $recentTransactions = SpiceTransaction::with(['house', 'relatedEvent'])
                                            ->latest()
                                            ->limit(20)
                                            ->get();

        // Graphique des transactions par jour (30 derniers jours)
        $transactionTrend = SpiceTransaction::selectRaw('
                                DATE(created_at) as date,
                                type,
                                SUM(amount) as total
                            ')
                            ->where('created_at', '>=', now()->subDays(30))
                            ->groupBy('date', 'type')
                            ->orderBy('date')
                            ->get()
                            ->groupBy('date');

        return view('dune-rp::admin.economy.index', compact(
            'economyStats', 
            'topHouses', 
            'recentTransactions',
            'transactionTrend'
        ));
    }

    /**
     * Display all transactions with advanced filtering.
     */
    public function transactions(Request $request)
    {
        $query = SpiceTransaction::with(['house', 'relatedEvent']);

        // Filtres avancés
        if ($request->filled('house_id')) {
            $query->where('house_id', $request->house_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('min_amount')) {
            $query->where('amount', '>=', $request->min_amount);
        }

        if ($request->filled('max_amount')) {
            $query->where('amount', '<=', $request->max_amount);
        }

        if ($request->filled('reason')) {
            $query->where('reason', 'like', '%' . $request->reason . '%');
        }

        $transactions = $query->latest()->paginate(25);
        $houses = House::orderBy('name')->get();

        return view('dune-rp::admin.economy.transactions', compact('transactions', 'houses'));
    }

    /**
     * Apply a global spice adjustment to all active houses.
     */
    public function globalAdjustment(Request $request)
    {
        $request->validate([
            'adjustment_type' => ['required', 'in:add,remove,multiply,set'],
            'amount' => ['required', 'numeric', 'min:0'],
            'reason' => ['required', 'string', 'max:255'],
            'target_houses' => ['nullable', 'in:all,active,rich,poor'],
        ]);

        $amount = $request->amount;
        $reason = 'Global Admin Adjustment by ' . auth()->user()->name . ': ' . $request->reason;
        $affectedHouses = 0;

        DB::transaction(function () use ($request, $amount, $reason, &$affectedHouses) {
            $query = House::query();
            
            // Sélection des maisons cibles
            switch ($request->target_houses ?? 'all') {
                case 'active':
                    $query->where('is_active', true);
                    break;
                case 'rich':
                    $query->where('is_active', true)->where('spice_reserves', '>', 1000);
                    break;
                case 'poor':
                    $query->where('is_active', true)->where('spice_reserves', '<', 100);
                    break;
                default:
                    $query->where('is_active', true);
            }

            $houses = $query->get();

            foreach ($houses as $house) {
                $success = true;
                $currentReserves = $house->spice_reserves;
                
                switch ($request->adjustment_type) {
                    case 'add':
                        $house->addSpice($amount, $reason);
                        break;
                    case 'remove':
                        $success = $house->removeSpice($amount, $reason);
                        break;
                    case 'multiply':
                        $newAmount = $currentReserves * $amount;
                        $diff = $newAmount - $currentReserves;
                        if ($diff > 0) {
                            $house->addSpice($diff, $reason);
                        } else {
                            $success = $house->removeSpice(abs($diff), $reason);
                        }
                        break;
                    case 'set':
                        if ($amount > $currentReserves) {
                            $house->addSpice($amount - $currentReserves, $reason);
                        } elseif ($amount < $currentReserves) {
                            $success = $house->removeSpice($currentReserves - $amount, $reason);
                        }
                        break;
                }

                if ($success) {
                    $affectedHouses++;
                }
            }
        });

        $message = "Global adjustment applied to {$affectedHouses} houses";

        return back()->with('success', $message);
    }

    /**
     * Delete a specific transaction and adjust house reserves.
     */
    public function deleteTransaction(SpiceTransaction $transaction)
    {
        DB::transaction(function () use ($transaction) {
            $house = $transaction->house;
            
            // Inverser la transaction
            if ($transaction->type === 'expense') {
                $house->addSpice($transaction->amount, 'Transaction deletion restoration');
            } elseif ($transaction->type === 'income') {
                $house->removeSpice($transaction->amount, 'Transaction deletion adjustment');
            }

            $transaction->delete();
        });

        return back()->with('success', 'Transaction deleted and house reserves adjusted');
    }

    /**
     * Generate comprehensive spice economy report.
     */
    public function report(Request $request)
    {
        $request->validate([
            'period' => ['required', 'in:day,week,month,quarter,year'],
        ]);

        $period = $request->period;
        $startDate = match($period) {
            'day' => now()->startOfDay(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'quarter' => now()->startOfQuarter(),
            'year' => now()->startOfYear(),
        };

        $report = [
            'period' => $period,
            'start_date' => $startDate,
            'end_date' => now(),
            'summary' => [
                'total_income' => SpiceTransaction::where('type', 'income')
                                                ->where('created_at', '>=', $startDate)
                                                ->sum('amount'),
                'total_expenses' => SpiceTransaction::where('type', 'expense')
                                                  ->where('created_at', '>=', $startDate)
                                                  ->sum('amount'),
                'total_transfers' => SpiceTransaction::where('type', 'transfer')
                                                   ->where('created_at', '>=', $startDate)
                                                   ->sum('amount'),
                'transactions_count' => SpiceTransaction::where('created_at', '>=', $startDate)->count(),
            ],
            'by_house' => House::where('is_active', true)
                              ->with(['spiceTransactions' => function($query) use ($startDate) {
                                  $query->where('created_at', '>=', $startDate);
                              }])
                              ->get()
                              ->map(function($house) {
                                  return [
                                      'name' => $house->name,
                                      'current_reserves' => $house->spice_reserves,
                                      'income' => $house->spiceTransactions->where('type', 'income')->sum('amount'),
                                      'expenses' => $house->spiceTransactions->where('type', 'expense')->sum('amount'),
                                      'net_flow' => $house->spiceTransactions->where('type', 'income')->sum('amount') - 
                                                   $house->spiceTransactions->where('type', 'expense')->sum('amount'),
                                  ];
                              })
                              ->sortByDesc('net_flow'),
            'top_income_sources' => SpiceTransaction::selectRaw('reason, SUM(amount) as total')
                                                  ->where('type', 'income')
                                                  ->where('created_at', '>=', $startDate)
                                                  ->whereNotNull('reason')
                                                  ->groupBy('reason')
                                                  ->orderByDesc('total')
                                                  ->limit(10)
                                                  ->get(),
            'top_expense_categories' => SpiceTransaction::selectRaw('reason, SUM(amount) as total')
                                                       ->where('type', 'expense')
                                                       ->where('created_at', '>=', $startDate)
                                                       ->whereNotNull('reason')
                                                       ->groupBy('reason')
                                                       ->orderByDesc('total')
                                                       ->limit(10)
                                                       ->get(),
        ];

        return view('dune-rp::admin.economy.report', compact('report'));
    }

    /**
     * Export transactions to CSV.
     */
    public function exportTransactions(Request $request)
    {
        $query = SpiceTransaction::with(['house', 'relatedEvent']);

        // Appliquer les mêmes filtres que la page des transactions
        if ($request->filled('house_id')) {
            $query->where('house_id', $request->house_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->orderBy('created_at')->get();

        $filename = 'spice_transactions_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function() use ($transactions) {
            $file = fopen('php://output', 'w');
            
            // Headers CSV
            fputcsv($file, [
                'ID',
                'Date',
                'House',
                'Type',
                'Amount',
                'Reason',
                'Related Event',
            ]);

            // Données
            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->id,
                    $transaction->created_at->format('Y-m-d H:i:s'),
                    $transaction->house->name,
                    $transaction->getTypeName(),
                    $transaction->amount,
                    $transaction->reason,
                    $transaction->relatedEvent ? $transaction->relatedEvent->title : '',
                ]);
            }

            fclose($file);
        }, 200, $headers);
    }
}
