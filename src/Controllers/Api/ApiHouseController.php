<?php

namespace Azuriom\Plugin\DuneRp\Controllers\Api;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Plugin\DuneRp\Models\House;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ApiHouseController extends Controller
{
    /**
     * Display a listing of active houses.
     */
    public function index(Request $request): JsonResponse
    {
        $query = House::where('is_active', true);

        // Filtres
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('min_influence')) {
            $query->where('influence_points', '>=', $request->min_influence);
        }

        if ($request->filled('min_spice')) {
            $query->where('spice_reserves', '>=', $request->min_spice);
        }

        // Tri
        $sortBy = $request->get('sort_by', 'influence_points');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if (in_array($sortBy, ['name', 'influence_points', 'spice_reserves', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        }

        // Sélection des champs et relations
        $houses = $query->select([
                'id', 'name', 'motto', 'homeworld', 'color', 
                'influence_points', 'spice_reserves', 'leader_id', 'created_at'
            ])
            ->with(['leader:id,name'])
            ->withCount(['characters as members_count' => function($query) {
                $query->where('is_approved', true)->where('status', 'alive');
            }])
            ->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $houses->items(),
            'pagination' => [
                'current_page' => $houses->currentPage(),
                'last_page' => $houses->lastPage(),
                'per_page' => $houses->perPage(),
                'total' => $houses->total(),
            ],
            'meta' => [
                'total_houses' => House::where('is_active', true)->count(),
                'total_spice' => House::where('is_active', true)->sum('spice_reserves'),
                'total_influence' => House::where('is_active', true)->sum('influence_points'),
            ],
        ]);
    }

    /**
     * Display the specified house.
     */
    public function show(House $house): JsonResponse
    {
        if (!$house->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'House not found or inactive',
            ], 404);
        }

        $house->load(['leader:id,name']);
        $house->loadCount(['characters as members_count' => function($query) {
            $query->where('is_approved', true)->where('status', 'alive');
        }]);

        // Statistiques supplémentaires
        $stats = [
            'total_events' => $house->events()->count(),
            'completed_events' => $house->events()->where('status', 'completed')->count(),
            'monthly_spice_income' => $house->spiceTransactions()
                                          ->where('type', 'income')
                                          ->where('created_at', '>=', now()->startOfMonth())
                                          ->sum('amount'),
            'monthly_spice_expenses' => $house->spiceTransactions()
                                            ->where('type', 'expense')
                                            ->where('created_at', '>=', now()->startOfMonth())
                                            ->sum('amount'),
        ];

        return response()->json([
            'success' => true,
            'data' => $house,
            'stats' => $stats,
        ]);
    }

    /**
     * Get house members.
     */
    public function members(House $house, Request $request): JsonResponse
    {
        if (!$house->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'House not found or inactive',
            ], 404);
        }

        $query = $house->characters()
                      ->where('is_approved', true)
                      ->where('is_public', true);

        // Filtres
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $members = $query->select(['id', 'name', 'title', 'status', 'created_at', 'user_id'])
                        ->with(['user:id,name'])
                        ->latest()
                        ->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'house' => [
                'id' => $house->id,
                'name' => $house->name,
                'color' => $house->color,
            ],
            'members' => $members->items(),
            'pagination' => [
                'current_page' => $members->currentPage(),
                'last_page' => $members->lastPage(),
                'per_page' => $members->perPage(),
                'total' => $members->total(),
            ],
        ]);
    }

    /**
     * Get house events.
     */
    public function events(House $house, Request $request): JsonResponse
    {
        if (!$house->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'House not found or inactive',
            ], 404);
        }

        $query = $house->events()->where('is_public', true);

        // Filtres
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('event_type')) {
            $query->where('event_type', $request->event_type);
        }

        $events = $query->select([
                'id', 'title', 'event_date', 'location', 'event_type', 
                'status', 'spice_cost', 'reward_spice', 'max_participants'
            ])
            ->latest('event_date')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'house' => [
                'id' => $house->id,
                'name' => $house->name,
                'color' => $house->color,
            ],
            'events' => $events->items(),
            'pagination' => [
                'current_page' => $events->currentPage(),
                'last_page' => $events->lastPage(),
                'per_page' => $events->perPage(),
                'total' => $events->total(),
            ],
        ]);
    }

    /**
     * Get house leaderboard rankings.
     */
    public function leaderboard(Request $request): JsonResponse
    {
        $category = $request->get('category', 'influence');
        $limit = min($request->get('limit', 10), 50); // Max 50

        $query = House::where('is_active', true);

        switch ($category) {
            case 'spice':
                $query->orderByDesc('spice_reserves');
                break;
            case 'members':
                $query->withCount(['characters as members_count' => function($q) {
                    $q->where('is_approved', true)->where('status', 'alive');
                }])->orderByDesc('members_count');
                break;
            default: // influence
                $query->orderByDesc('influence_points');
        }

        $houses = $query->select(['id', 'name', 'color', 'influence_points', 'spice_reserves'])
                       ->limit($limit)
                       ->get();

        // Ajouter le nombre de membres pour tous
        if ($category !== 'members') {
            $houses->load(['characters' => function($query) {
                $query->select(['id', 'house_id'])
                      ->where('is_approved', true)
                      ->where('status', 'alive');
            }]);
            
            $houses = $houses->map(function($house) {
                $house->members_count = $house->characters->count();
                unset($house->characters);
                return $house;
            });
        }

        return response()->json([
            'success' => true,
            'category' => $category,
            'leaderboard' => $houses,
            'meta' => [
                'total_houses' => House::where('is_active', true)->count(),
                'generated_at' => now()->toISOString(),
            ],
        ]);
    }

    /**
     * Get house statistics summary.
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'total_active_houses' => House::where('is_active', true)->count(),
            'total_spice_reserves' => House::where('is_active', true)->sum('spice_reserves'),
            'total_influence_points' => House::where('is_active', true)->sum('influence_points'),
            'average_spice_per_house' => House::where('is_active', true)->avg('spice_reserves'),
            'average_influence_per_house' => House::where('is_active', true)->avg('influence_points'),
            'houses_with_leaders' => House::where('is_active', true)->whereNotNull('leader_id')->count(),
            'houses_by_influence_tier' => [
                'emperor' => House::where('is_active', true)->where('influence_points', '>=', 10000)->count(),
                'major_house' => House::where('is_active', true)
                                    ->where('influence_points', '>=', 5000)
                                    ->where('influence_points', '<', 10000)
                                    ->count(),
                'great_house' => House::where('is_active', true)
                                    ->where('influence_points', '>=', 2000)
                                    ->where('influence_points', '<', 5000)
                                    ->count(),
                'minor_house' => House::where('is_active', true)
                                    ->where('influence_points', '>=', 500)
                                    ->where('influence_points', '<', 2000)
                                    ->count(),
                'emerging_house' => House::where('is_active', true)
                                        ->where('influence_points', '<', 500)
                                        ->count(),
            ],
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'generated_at' => now()->toISOString(),
        ]);
    }
}
