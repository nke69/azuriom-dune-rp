<?php

namespace Azuriom\Plugin\DuneRp\Controllers\Admin;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Plugin\DuneRp\Models\House;
use Azuriom\Plugin\DuneRp\Models\Character;
use Azuriom\Plugin\DuneRp\Models\RpEvent;
use Azuriom\Plugin\DuneRp\Models\SpiceTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    /**
     * Display the plugin admin dashboard.
     */
    public function index()
    {
        // Statistiques générales
        $stats = [
            'houses' => [
                'total' => House::count(),
                'active' => House::where('is_active', true)->count(),
                'with_leader' => House::whereNotNull('leader_id')->count(),
            ],
            'characters' => [
                'total' => Character::count(),
                'approved' => Character::where('is_approved', true)->count(),
                'pending' => Character::where('is_approved', false)->count(),
                'public' => Character::where('is_public', true)->count(),
            ],
            'events' => [
                'total' => RpEvent::count(),
                'planned' => RpEvent::where('status', 'planned')->count(),
                'ongoing' => RpEvent::where('status', 'ongoing')->count(),
                'completed' => RpEvent::where('status', 'completed')->count(),
            ],
            'spice' => [
                'total_reserves' => House::where('is_active', true)->sum('spice_reserves'),
                'total_transactions' => SpiceTransaction::count(),
                'daily_volume' => SpiceTransaction::whereDate('created_at', today())->sum('amount'),
            ],
        ];

        // Top 5 des maisons par influence
        $topHouses = House::where('is_active', true)
                         ->orderByDesc('influence_points')
                         ->limit(5)
                         ->get(['name', 'influence_points', 'spice_reserves']);

        // Personnages récents en attente
        $pendingCharacters = Character::where('is_approved', false)
                                     ->with(['user', 'house'])
                                     ->latest()
                                     ->limit(5)
                                     ->get();

        // Événements à venir
        $upcomingEvents = RpEvent::where('status', 'planned')
                                ->where('event_date', '>', now())
                                ->orderBy('event_date')
                                ->limit(5)
                                ->get();

        // Graphique des créations sur 30 jours
        $creationStats = [
            'dates' => [],
            'houses' => [],
            'characters' => [],
            'events' => [],
        ];

        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $creationStats['dates'][] = $date->format('d/m');
            
            $creationStats['houses'][] = House::whereDate('created_at', $date)->count();
            $creationStats['characters'][] = Character::whereDate('created_at', $date)->count();
            $creationStats['events'][] = RpEvent::whereDate('created_at', $date)->count();
        }

        // Transactions d'épice récentes
        $recentTransactions = SpiceTransaction::with('house')
                                             ->latest()
                                             ->limit(10)
                                             ->get();

        // Alertes système
        $alerts = [];
        
        // Alerte si trop de personnages en attente
        if ($stats['characters']['pending'] > 10) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "Il y a {$stats['characters']['pending']} personnages en attente d'approbation.",
                'action' => route('admin.dune-rp.characters.pending'),
                'action_text' => 'Voir les personnages',
            ];
        }

        // Alerte si une maison a trop d'épice
        $richestHouse = House::where('is_active', true)->orderByDesc('spice_reserves')->first();
        if ($richestHouse && $richestHouse->spice_reserves > 100000) {
            $alerts[] = [
                'type' => 'info',
                'message' => "La maison {$richestHouse->name} possède plus de 100,000 tonnes d'épice.",
                'action' => route('admin.dune-rp.houses.show', $richestHouse),
                'action_text' => 'Voir la maison',
            ];
        }

        // Alerte si aucun événement prévu
        if ($stats['events']['planned'] == 0) {
            $alerts[] = [
                'type' => 'info',
                'message' => "Aucun événement n'est actuellement planifié.",
                'action' => route('admin.dune-rp.events.create'),
                'action_text' => 'Créer un événement',
            ];
        }

        return view('dune-rp::admin.dashboard', compact(
            'stats',
            'topHouses',
            'pendingCharacters',
            'upcomingEvents',
            'creationStats',
            'recentTransactions',
            'alerts'
        ));
    }

    /**
     * Export statistics as JSON.
     */
    public function exportStats()
    {
        $data = [
            'export_date' => now()->toIso8601String(),
            'plugin_version' => '1.1.0',
            'statistics' => [
                'houses' => House::count(),
                'characters' => Character::count(),
                'events' => RpEvent::count(),
                'spice_transactions' => SpiceTransaction::count(),
                'total_spice' => House::sum('spice_reserves'),
                'total_influence' => House::sum('influence_points'),
            ],
            'detailed_stats' => [
                'houses_by_status' => [
                    'active' => House::where('is_active', true)->count(),
                    'inactive' => House::where('is_active', false)->count(),
                ],
                'characters_by_status' => Character::selectRaw('status, COUNT(*) as count')
                                                  ->groupBy('status')
                                                  ->pluck('count', 'status'),
                'events_by_type' => RpEvent::selectRaw('event_type, COUNT(*) as count')
                                          ->groupBy('event_type')
                                          ->pluck('count', 'event_type'),
            ],
        ];

        $filename = 'dune_rp_stats_' . now()->format('Y-m-d_H-i-s') . '.json';

        return response()->json($data, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
