<?php

namespace Azuriom\Plugin\DuneRp\Controllers;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Plugin\DuneRp\Models\House;
use Azuriom\Plugin\DuneRp\Models\Character;

class HouseController extends Controller
{
    /**
     * Display all active houses.
     */
    public function index()
    {
        $houses = House::where('is_active', true)
                      ->with(['leader'])
                      ->withCount(['characters as active_characters_count' => function($query) {
                          $query->where('status', 'alive')
                                ->where('is_approved', true);
                      }])
                      ->orderByDesc('influence_points')
                      ->paginate(12);

        return view('dune-rp::houses.index', compact('houses'));
    }

    /**
     * Display a specific house details.
     */
    public function show(House $house)
    {
        // Vérifier que la maison est active
        if (!$house->is_active) {
            abort(404);
        }

        // Charger les relations nécessaires
        $house->load([
            'leader',
            'characters' => function($query) {
                $query->where('is_public', true)
                      ->where('is_approved', true)
                      ->where('status', 'alive')
                      ->with('user')
                      ->orderBy('created_at', 'desc');
            },
            'events' => function($query) {
                $query->where('is_public', true)
                      ->latest('event_date')
                      ->limit(5);
            }
        ]);

        // Récupérer les dernières transactions d'épice
        $recentTransactions = $house->spiceTransactions()
                                   ->with('relatedEvent')
                                   ->latest()
                                   ->limit(10)
                                   ->get();

        // Statistiques de la maison
        $houseStats = [
            'total_members' => $house->characters()->where('is_approved', true)->count(),
            'active_members' => $house->characters()
                                     ->where('is_approved', true)
                                     ->where('status', 'alive')
                                     ->count(),
            'total_events' => $house->events()->count(),
            'completed_events' => $house->events()->where('status', 'completed')->count(),
            'spice_earned_this_month' => $house->spiceTransactions()
                                              ->where('type', 'income')
                                              ->where('created_at', '>=', now()->startOfMonth())
                                              ->sum('amount'),
        ];

        return view('dune-rp::houses.show', compact(
            'house', 
            'recentTransactions', 
            'houseStats'
        ));
    }

    /**
     * Display house recruitment page.
     */
    public function recruitment()
    {
        $recruitingHouses = House::where('is_active', true)
                                ->whereNotNull('description')
                                ->with(['leader'])
                                ->withCount(['characters as active_characters_count' => function($query) {
                                    $query->where('status', 'alive')
                                          ->where('is_approved', true);
                                }])
                                ->orderByDesc('influence_points')
                                ->get();

        return view('dune-rp::houses.recruitment', compact('recruitingHouses'));
    }

    /**
     * Display house comparison page.
     */
    public function compare()
    {
        $houses = House::where('is_active', true)
                      ->with(['leader'])
                      ->withCount(['characters as members_count' => function($query) {
                          $query->where('is_approved', true)
                                ->where('status', 'alive');
                      }])
                      ->orderByDesc('influence_points')
                      ->get();

        return view('dune-rp::houses.compare', compact('houses'));
    }

    /**
     * Display house leaderboard.
     */
    public function leaderboard()
    {
        $categories = [
            'influence' => House::where('is_active', true)
                               ->orderByDesc('influence_points')
                               ->limit(10)
                               ->get(),
            'spice' => House::where('is_active', true)
                           ->orderByDesc('spice_reserves')
                           ->limit(10)
                           ->get(),
            'members' => House::where('is_active', true)
                             ->withCount(['characters as members_count' => function($query) {
                                 $query->where('is_approved', true)
                                       ->where('status', 'alive');
                             }])
                             ->orderByDesc('members_count')
                             ->limit(10)
                             ->get(),
        ];

        return view('dune-rp::houses.leaderboard', compact('categories'));
    }
}
