<?php

namespace Azuriom\Plugin\DuneRp\Controllers;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Plugin\DuneRp\Models\House;
use Azuriom\Plugin\DuneRp\Models\SpiceTransaction;
use Illuminate\Http\Request;

class SpiceController extends Controller
{
    /**
     * Display the spice economy overview.
     */
    public function index()
    {
        // Top 10 des maisons les plus riches en épice
        $topHouses = House::where('is_active', true)
                         ->orderByDesc('spice_reserves')
                         ->limit(10)
                         ->get();

        // Transactions récentes
        $recentTransactions = SpiceTransaction::with(['house', 'relatedEvent'])
                                            ->latest()
                                            ->limit(20)
                                            ->get();

        // Statistiques économiques
        $economyStats = [
            'total_spice' => House::where('is_active', true)->sum('spice_reserves'),
            'total_transactions' => SpiceTransaction::count(),
            'average_reserves' => House::where('is_active', true)->avg('spice_reserves'),
            'richest_house' => House::where('is_active', true)
                                  ->orderByDesc('spice_reserves')
                                  ->first(),
            'daily_income' => SpiceTransaction::where('type', 'income')
                                            ->whereDate('created_at', today())
                                            ->sum('amount'),
            'daily_expenses' => SpiceTransaction::where('type', 'expense')
                                              ->whereDate('created_at', today())
                                              ->sum('amount'),
            'houses_count' => House::where('is_active', true)->count(),
        ];

        return view('dune-rp::economy.index', compact('topHouses', 'recentTransactions', 'economyStats'));
    }

    /**
     * Display spice transactions for a specific house.
     */
    public function houseTransactions(House $house, Request $request)
    {
        if (!$house->is_active) {
            abort(404);
        }

        $query = $house->spiceTransactions()->with('relatedEvent');

        // Filtres
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->latest()->paginate(25);

        // Statistiques spécifiques à la maison
        $houseStats = [
            'current_reserves' => $house->spice_reserves,
            'total_income' => $house->spiceTransactions()->where('type', 'income')->sum('amount'),
            'total_expenses' => $house->spiceTransactions()->where('type', 'expense')->sum('amount'),
            'monthly_income' => $house->spiceTransactions()
                                   ->where('type', 'income')
                                   ->where('created_at', '>=', now()->startOfMonth())
                                   ->sum('amount'),
            'monthly_expenses' => $house->spiceTransactions()
                                    ->where('type', 'expense')
                                    ->where('created_at', '>=', now()->startOfMonth())
                                    ->sum('amount'),
        ];

        return view('dune-rp::economy.house-transactions', compact('house', 'transactions', 'houseStats'));
    }

    /**
     * Display spice market analysis.
     */
    public function market()
    {
        // Analyse de marché pour les 30 derniers jours
        $marketData = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $marketData[] = [
                'date' => $date->format('Y-m-d'),
                'total_spice' => House::where('is_active', true)
                                    ->where('updated_at', '<=', $date->endOfDay())
                                    ->sum('spice_reserves'),
                'transactions' => SpiceTransaction::whereDate('created_at', $date)->count(),
                'volume' => SpiceTransaction::whereDate('created_at', $date)->sum('amount'),
            ];
        }

        // Trending houses (gains d'influence récents)
        $trendingHouses = House::where('is_active', true)
                              ->whereHas('spiceTransactions', function($query) {
                                  $query->where('created_at', '>=', now()->subWeek());
                              })
                              ->withSum(['spiceTransactions' => function($query) {
                                  $query->where('type', 'income')
                                        ->where('created_at', '>=', now()->subWeek());
                              }], 'amount')
                              ->orderByDesc('spice_transactions_sum_amount')
                              ->limit(5)
                              ->get();

        return view('dune-rp::economy.market', compact('marketData', 'trendingHouses'));
    }

    /**
     * Display global spice flow analysis.
     */
    public function flow()
    {
        $flowData = [
            'income_sources' => SpiceTransaction::selectRaw('reason, SUM(amount) as total')
                                               ->where('type', 'income')
                                               ->where('created_at', '>=', now()->subMonth())
                                               ->whereNotNull('reason')
                                               ->groupBy('reason')
                                               ->orderByDesc('total')
                                               ->limit(10)
                                               ->get(),
            'expense_categories' => SpiceTransaction::selectRaw('reason, SUM(amount) as total')
                                                  ->where('type', 'expense')
                                                  ->where('created_at', '>=', now()->subMonth())
                                                  ->whereNotNull('reason')
                                                  ->groupBy('reason')
                                                  ->orderByDesc('total')
                                                  ->limit(10)
                                                  ->get(),
            'house_flows' => House::where('is_active', true)
                                 ->with(['spiceTransactions' => function($query) {
                                     $query->where('created_at', '>=', now()->subMonth());
                                 }])
                                 ->get()
                                 ->map(function($house) {
                                     return [
                                         'name' => $house->name,
                                         'income' => $house->spiceTransactions->where('type', 'income')->sum('amount'),
                                         'expenses' => $house->spiceTransactions->where('type', 'expense')->sum('amount'),
                                         'net' => $house->spiceTransactions->where('type', 'income')->sum('amount') - 
                                                 $house->spiceTransactions->where('type', 'expense')->sum('amount'),
                                     ];
                                 })
                                 ->sortByDesc('net'),
        ];

        return view('dune-rp::economy.flow', compact('flowData'));
    }
}
