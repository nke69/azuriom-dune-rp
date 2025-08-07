<?php

namespace Azuriom\Plugin\DuneRp\Controllers\Api;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Plugin\DuneRp\Models\House;
use Azuriom\Plugin\DuneRp\Models\SpiceTransaction;
use Azuriom\Plugin\DuneRp\Models\Character;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ApiSpiceController extends Controller
{
    /**
     * Get global spice economy overview.
     */
    public function economy(): JsonResponse
    {
        $economyData = [
            'total_spice' => House::where('is_active', true)->sum('spice_reserves'),
            'total_houses' => House::where('is_active', true)->count(),
            'average_reserves' => House::where('is_active', true)->avg('spice_reserves'),
            'richest_house' => House::where('is_active', true)
                                  ->select(['id', 'name', 'spice_reserves'])
                                  ->orderByDesc('spice_reserves')
                                  ->first(),
            'poorest_house' => House::where('is_active', true)
                                   ->select(['id', 'name', 'spice_reserves'])
                                   ->orderBy('spice_reserves')
                                   ->first(),
            'total_transactions_today' => SpiceTransaction::whereDate('created_at', today())->count(),
            'daily_volume' => SpiceTransaction::whereDate('created_at', today())->sum('amount'),
            'weekly_stats' => [
                'income' => SpiceTransaction::where('type', 'income')
                                          ->where('created_at', '>=', now()->subWeek())
                                          ->sum('amount'),
                'expenses' => SpiceTransaction::where('type', 'expense')
                                            ->where('created_at', '>=', now()->subWeek())
                                            ->sum('amount'),
                'net_flow' => SpiceTransaction::where('created_at', '>=', now()->subWeek())
                                            ->selectRaw('
                                                SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) - 
                                                SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) as net
                                            ')
                                            ->value('net') ?? 0,
            ],
            'distribution' => [
                'top_10_percent' => House::where('is_active', true)
                                        ->orderByDesc('spice_reserves')
                                        ->limit(max(1, ceil(House::where('is_active', true)->count() * 0.1)))
                                        ->sum('spice_reserves'),
                'gini_coefficient' => $this->calculateGiniCoefficient(),
            ],
        ];

        return response()->json([
            'success' => true,
            'economy' => $economyData,
            'generated_at' => now()->toISOString(),
        ]);
    }

    /**
     * Get spice balance for a specific house.
     */
    public function houseBalance(House $house): JsonResponse
    {
        if (!$house->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'House not found or inactive',
            ], 404);
        }

        $balance = [
            'current_reserves' => $house->spice_reserves,
            'influence_points' => $house->influence_points,
            'monthly_summary' => [
                'income' => $house->spiceTransactions()
                                 ->where('type', 'income')
                                 ->where('created_at', '>=', now()->startOfMonth())
                                 ->sum('amount'),
                'expenses' => $house->spiceTransactions()
                                   ->where('type', 'expense')
                                   ->where('created_at', '>=', now()->startOfMonth())
                                   ->sum('amount'),
                'transactions_count' => $house->spiceTransactions()
                                             ->where('created_at', '>=', now()->startOfMonth())
                                             ->count(),
            ],
            'ranking' => [
                'spice_rank' => House::where('is_active', true)
                                   ->where('spice_reserves', '>', $house->spice_reserves)
                                   ->count() + 1,
                'influence_rank' => House::where('is_active', true)
                                        ->where('influence_points', '>', $house->influence_points)
                                        ->count() + 1,
                'total_houses' => House::where('is_active', true)->count(),
            ],
            'recent_transactions' => $house->spiceTransactions()
                                          ->select(['type', 'amount', 'reason', 'created_at'])
                                          ->latest()
                                          ->limit(10)
                                          ->get(),
        ];

        return response()->json([
            'success' => true,
            'house' => [
                'id' => $house->id,
                'name' => $house->name,
                'color' => $house->color,
            ],
            'balance' => $balance,
        ]);
    }

    /**
     * Get spice market trends.
     */
    public function trends(Request $request): JsonResponse
    {
        $days = min($request->get('days', 30), 90); // Max 3 mois

        $dailyData = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayData = [
                'date' => $date->format('Y-m-d'),
                'total_spice' => House::where('is_active', true)
                                    ->where('updated_at', '<=', $date->endOfDay())
                                    ->sum('spice_reserves'),
                'transactions' => SpiceTransaction::whereDate('created_at', $date)->count(),
                'volume' => SpiceTransaction::whereDate('created_at', $date)->sum('amount'),
                'income' => SpiceTransaction::where('type', 'income')
                                          ->whereDate('created_at', $date)
                                          ->sum('amount'),
                'expenses' => SpiceTransaction::where('type', 'expense')
                                            ->whereDate('created_at', $date)
                                            ->sum('amount'),
            ];
            $dayData['net_flow'] = $dayData['income'] - $dayData['expenses'];
            $dailyData[] = $dayData;
        }

        // Top movers (maisons avec plus gros changements récents)
        $topMovers = House::where('is_active', true)
                         ->select(['id', 'name', 'spice_reserves'])
                         ->with(['spiceTransactions' => function($query) {
                             $query->where('created_at', '>=', now()->subWeek())
                                   ->select(['house_id', 'type', 'amount']);
                         }])
                         ->get()
                         ->map(function($house) {
                             $weeklyNet = $house->spiceTransactions->where('type', 'income')->sum('amount') -
                                         $house->spiceTransactions->where('type', 'expense')->sum('amount');
                             return [
                                 'house_id' => $house->id,
                                 'house_name' => $house->name,
                                 'current_reserves' => $house->spice_reserves,
                                 'weekly_change' => $weeklyNet,
                                 'change_percent' => $house->spice_reserves > 0 
                                     ? round(($weeklyNet / $house->spice_reserves) * 100, 2) 
                                     : 0,
                             ];
                         })
                         ->sortByDesc('weekly_change')
                         ->values();

        return response()->json([
            'success' => true,
            'period_days' => $days,
            'daily_trends' => $dailyData,
            'top_gainers' => $topMovers->take(5),
            'top_losers' => $topMovers->sortBy('weekly_change')->take(5)->values(),
            'summary' => [
                'total_volume' => array_sum(array_column($dailyData, 'volume')),
                'average_daily_volume' => round(array_sum(array_column($dailyData, 'volume')) / $days, 2),
                'trend_direction' => $this->calculateTrendDirection($dailyData),
            ],
            'generated_at' => now()->toISOString(),
        ]);
    }

    /**
     * Transfer spice between houses (authenticated endpoint).
     */
    public function transfer(Request $request): JsonResponse
    {
        $request->validate([
            'from_house_id' => ['required', 'exists:dune_rp_houses,id'],
            'to_house_id' => ['required', 'exists:dune_rp_houses,id', 'different:from_house_id'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:100000'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $user = auth()->user();
        $fromHouse = House::find($request->from_house_id);
        $toHouse = House::find($request->to_house_id);

        // Vérifier que l'utilisateur peut transférer depuis cette maison
        $userCharacter = $user->characters()
                             ->where('house_id', $fromHouse->id)
                             ->where('is_approved', true)
                             ->first();

        if (!$userCharacter) {
            return response()->json([
                'success' => false,
                'message' => 'You are not a member of the source house',
            ], 403);
        }

        // Vérifier que les transferts sont activés
        if (!setting('dune_rp.enable_spice_transfers', false)) {
            return response()->json([
                'success' => false,
                'message' => 'Spice transfers are currently disabled',
            ], 403);
        }

        // Vérifier les réserves
        if (!$fromHouse->hasSpiceReserves($request->amount)) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient spice reserves',
            ], 400);
        }

        DB::transaction(function () use ($fromHouse, $toHouse, $request, $user) {
            $reason = $request->reason ?? 'Spice transfer';
            $transferNote = "Transfer to {$toHouse->name} by {$user->name}: {$reason}";
            $receiveNote = "Transfer from {$fromHouse->name} by {$user->name}: {$reason}";

            // Débiter la maison source
            $fromHouse->removeSpice($request->amount, $transferNote);

            // Créditer la maison destination
            $toHouse->addSpice($request->amount, $receiveNote);
        });

        return response()->json([
            'success' => true,
            'message' => 'Spice transfer completed successfully',
            'transfer' => [
                'from_house' => $fromHouse->name,
                'to_house' => $toHouse->name,
                'amount' => $request->amount,
                'reason' => $request->reason,
                'transferred_by' => $user->name,
                'timestamp' => now()->toISOString(),
            ],
        ]);
    }

    /**
     * Get spice transaction history.
     */
    public function transactions(Request $request): JsonResponse
    {
        $query = SpiceTransaction::with(['house:id,name,color']);

        // Filtres
        if ($request->filled('house_id')) {
            $query->where('house_id', $request->house_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('min_amount')) {
            $query->where('amount', '>=', $request->min_amount);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->select([
                'id', 'house_id', 'type', 'amount', 'reason', 'created_at'
            ])
            ->latest()
            ->paginate($request->get('per_page', 25));

        return response()->json([
            'success' => true,
            'transactions' => $transactions->items(),
            'pagination' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
            ],
            'summary' => [
                'total_volume' => $query->sum('amount'),
                'income_total' => $query->where('type', 'income')->sum('amount'),
                'expense_total' => $query->where('type', 'expense')->sum('amount'),
            ],
        ]);
    }

    /**
     * Calculate Gini coefficient for spice distribution.
     */
    private function calculateGiniCoefficient(): float
    {
        $reserves = House::where('is_active', true)
                        ->pluck('spice_reserves')
                        ->sort()
                        ->values()
                        ->toArray();

        if (empty($reserves)) {
            return 0;
        }

        $n = count($reserves);
        $sum = array_sum($reserves);

        if ($sum == 0) {
            return 0;
        }

        $gini = 0;
        for ($i = 1; $i <= $n; $i++) {
            $gini += (2 * $i - $n - 1) * $reserves[$i - 1];
        }

        return $gini / ($n * $sum);
    }

    /**
     * Calculate trend direction from daily data.
     */
    private function calculateTrendDirection(array $dailyData): string
    {
        if (count($dailyData) < 2) {
            return 'stable';
        }

        $firstHalf = array_slice($dailyData, 0, floor(count($dailyData) / 2));
        $secondHalf = array_slice($dailyData, floor(count($dailyData) / 2));

        $firstAvg = array_sum(array_column($firstHalf, 'total_spice')) / count($firstHalf);
        $secondAvg = array_sum(array_column($secondHalf, 'total_spice')) / count($secondHalf);

        $change = ($secondAvg - $firstAvg) / $firstAvg;

        if ($change > 0.05) {
            return 'rising';
        } elseif ($change < -0.05) {
            return 'falling';
        } else {
            return 'stable';
        }
    }
}
