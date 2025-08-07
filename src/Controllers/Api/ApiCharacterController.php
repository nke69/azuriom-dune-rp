<?php

namespace Azuriom\Plugin\DuneRp\Controllers\Api;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Plugin\DuneRp\Models\Character;
use Azuriom\Plugin\DuneRp\Models\House;
use Azuriom\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ApiCharacterController extends Controller
{
    /**
     * Display a listing of public approved characters.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Character::where('is_public', true)
                         ->where('is_approved', true);

        // Filtres
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($q) use ($request) {
                      $q->where('name', 'like', '%' . $request->search . '%');
                  });
        }

        if ($request->filled('house_id')) {
            $query->where('house_id', $request->house_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('has_abilities')) {
            if ($request->has_abilities === 'true') {
                $query->whereNotNull('special_abilities');
            } else {
                $query->whereNull('special_abilities');
            }
        }

        // Tri
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if (in_array($sortBy, ['name', 'age', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        }

        $characters = $query->select([
                'id', 'name', 'title', 'house_id', 'status', 'age', 
                'birthworld', 'spice_addiction_level', 'special_abilities', 
                'avatar_url', 'created_at', 'user_id'
            ])
            ->with([
                'house:id,name,color',
                'user:id,name'
            ])
            ->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $characters->items(),
            'pagination' => [
                'current_page' => $characters->currentPage(),
                'last_page' => $characters->lastPage(),
                'per_page' => $characters->perPage(),
                'total' => $characters->total(),
            ],
            'meta' => [
                'total_characters' => Character::where('is_approved', true)->count(),
                'total_public' => Character::where('is_public', true)->where('is_approved', true)->count(),
                'houses_represented' => Character::where('is_approved', true)
                                               ->whereNotNull('house_id')
                                               ->distinct('house_id')
                                               ->count(),
            ],
        ]);
    }

    /**
     * Display the specified character.
     */
    public function show(Character $character): JsonResponse
    {
        if (!$character->is_public || !$character->is_approved) {
            return response()->json([
                'success' => false,
                'message' => 'Character not found or not public',
            ], 404);
        }

        $character->load([
            'house:id,name,color,motto',
            'user:id,name'
        ]);

        return response()->json([
            'success' => true,
            'data' => $character,
        ]);
    }

    /**
     * Get characters by user.
     */
    public function byUser(User $user, Request $request): JsonResponse
    {
        $query = $user->characters()
                     ->where('is_public', true)
                     ->where('is_approved', true);

        $characters = $query->select([
                'id', 'name', 'title', 'house_id', 'status', 'age',
                'avatar_url', 'created_at'
            ])
            ->with(['house:id,name,color'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
            ],
            'characters' => $characters,
        ]);
    }

    /**
     * Get characters by house.
     */
    public function byHouse(House $house, Request $request): JsonResponse
    {
        if (!$house->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'House not found or inactive',
            ], 404);
        }

        $query = $house->characters()
                      ->where('is_public', true)
                      ->where('is_approved', true);

        // Filtres
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $characters = $query->select([
                'id', 'name', 'title', 'status', 'age', 'spice_addiction_level',
                'special_abilities', 'avatar_url', 'created_at', 'user_id'
            ])
            ->with(['user:id,name'])
            ->latest()
            ->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'house' => [
                'id' => $house->id,
                'name' => $house->name,
                'color' => $house->color,
                'motto' => $house->motto,
            ],
            'characters' => $characters->items(),
            'pagination' => [
                'current_page' => $characters->currentPage(),
                'last_page' => $characters->lastPage(),
                'per_page' => $characters->perPage(),
                'total' => $characters->total(),
            ],
        ]);
    }

    /**
     * Get character statistics.
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'total_approved' => Character::where('is_approved', true)->count(),
            'total_public' => Character::where('is_public', true)->where('is_approved', true)->count(),
            'pending_approval' => Character::where('is_approved', false)->count(),
            'by_status' => Character::where('is_approved', true)
                                  ->selectRaw('status, COUNT(*) as count')
                                  ->groupBy('status')
                                  ->pluck('count', 'status'),
            'by_house' => House::withCount(['characters' => function($query) {
                              $query->where('is_approved', true)->where('status', 'alive');
                          }])
                          ->where('is_active', true)
                          ->get()
                          ->pluck('characters_count', 'name'),
            'by_addiction_level' => Character::where('is_approved', true)
                                           ->selectRaw('spice_addiction_level, COUNT(*) as count')
                                           ->groupBy('spice_addiction_level')
                                           ->pluck('count', 'spice_addiction_level'),
            'average_age' => Character::where('is_approved', true)
                                    ->whereNotNull('age')
                                    ->avg('age'),
            'characters_with_abilities' => Character::where('is_approved', true)
                                                  ->whereNotNull('special_abilities')
                                                  ->count(),
            'most_common_abilities' => Character::where('is_approved', true)
                                               ->whereNotNull('special_abilities')
                                               ->get()
                                               ->pluck('special_abilities')
                                               ->flatten()
                                               ->countBy()
                                               ->sortDesc()
                                               ->take(10),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'generated_at' => now()->toISOString(),
        ]);
    }

    /**
     * Get character creation trends.
     */
    public function trends(Request $request): JsonResponse
    {
        $days = min($request->get('days', 30), 365); // Max 1 année

        $trends = Character::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                          ->where('created_at', '>=', now()->subDays($days))
                          ->where('is_approved', true)
                          ->groupBy('date')
                          ->orderBy('date')
                          ->get();

        $houseGrowth = House::with(['characters' => function($query) use ($days) {
                              $query->select(['id', 'house_id', 'created_at'])
                                    ->where('is_approved', true)
                                    ->where('created_at', '>=', now()->subDays($days));
                          }])
                          ->where('is_active', true)
                          ->get()
                          ->map(function($house) {
                              return [
                                  'house_name' => $house->name,
                                  'new_members' => $house->characters->count(),
                              ];
                          })
                          ->sortByDesc('new_members')
                          ->values();

        return response()->json([
            'success' => true,
            'period_days' => $days,
            'daily_creations' => $trends,
            'house_growth' => $houseGrowth,
            'summary' => [
                'total_created' => $trends->sum('count'),
                'average_per_day' => round($trends->avg('count'), 2),
                'peak_day' => $trends->sortByDesc('count')->first(),
            ],
            'generated_at' => now()->toISOString(),
        ]);
    }
}
