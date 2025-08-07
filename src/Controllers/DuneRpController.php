<?php

namespace Azuriom\Plugin\DuneRp\Controllers;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Plugin\DuneRp\Models\House;
use Azuriom\Plugin\DuneRp\Models\Character;
use Azuriom\Plugin\DuneRp\Models\RpEvent;

class DuneRpController extends Controller
{
    /**
     * Display the plugin homepage.
     */
    public function index()
    {
        // Top 5 maisons les plus influentes
        $houses = House::where('is_active', true)
                      ->orderByDesc('influence_points')
                      ->limit(5)
                      ->get();

        // Prochains événements publics
        $upcomingEvents = RpEvent::where('event_date', '>', now())
                                ->where('is_public', true)
                                ->where('status', 'planned')
                                ->orderBy('event_date')
                                ->limit(3)
                                ->with(['organizer', 'organizerHouse'])
                                ->get();

        // Derniers personnages créés
        $recentCharacters = Character::where('is_public', true)
                                   ->where('is_approved', true)
                                   ->with(['user', 'house'])
                                   ->latest()
                                   ->limit(6)
                                   ->get();

        // Statistiques générales
        $statistics = [
            'total_houses' => House::where('is_active', true)->count(),
            'total_characters' => Character::where('is_approved', true)->count(),
            'total_spice' => House::where('is_active', true)->sum('spice_reserves'),
            'active_events' => RpEvent::where('status', 'ongoing')->count(),
        ];

        return view('dune-rp::index', compact(
            'houses', 
            'upcomingEvents', 
            'recentCharacters', 
            'statistics'
        ));
    }

    /**
     * Display server statistics page.
     */
    public function statistics()
    {
        $stats = [
            'houses' => [
                'total' => House::where('is_active', true)->count(),
                'with_members' => House::where('is_active', true)
                                      ->whereHas('characters', function($query) {
                                          $query->where('status', 'alive')
                                                ->where('is_approved', true);
                                      })->count(),
                'most_influential' => House::where('is_active', true)
                                          ->orderByDesc('influence_points')
                                          ->first(),
                'richest' => House::where('is_active', true)
                                 ->orderByDesc('spice_reserves')
                                 ->first(),
            ],
            'characters' => [
                'total' => Character::where('is_approved', true)->count(),
                'alive' => Character::where('is_approved', true)
                                  ->where('status', 'alive')
                                  ->count(),
                'houses_distribution' => House::withCount(['characters' => function($query) {
                    $query->where('is_approved', true)->where('status', 'alive');
                }])->where('is_active', true)->get(),
            ],
            'economy' => [
                'total_spice' => House::where('is_active', true)->sum('spice_reserves'),
                'average_reserves' => House::where('is_active', true)->avg('spice_reserves'),
                'recent_transactions' => \Azuriom\Plugin\DuneRp\Models\SpiceTransaction::with('house')
                                                                                      ->latest()
                                                                                      ->limit(10)
                                                                                      ->get(),
            ],
            'events' => [
                'total_completed' => RpEvent::where('status', 'completed')->count(),
                'ongoing' => RpEvent::where('status', 'ongoing')->count(),
                'upcoming' => RpEvent::where('status', 'planned')
                                   ->where('event_date', '>', now())
                                   ->count(),
            ],
        ];

        return view('dune-rp::statistics', compact('stats'));
    }

    /**
     * Display the lore/universe page.
     */
    public function lore()
    {
        $majorHouses = House::where('is_active', true)
                           ->where('influence_points', '>=', 2000)
                           ->orderByDesc('influence_points')
                           ->get();

        return view('dune-rp::lore', compact('majorHouses'));
    }
}
