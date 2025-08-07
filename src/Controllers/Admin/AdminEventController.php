<?php

namespace Azuriom\Plugin\DuneRp\Controllers\Admin;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Models\User;
use Azuriom\Plugin\DuneRp\Models\RpEvent;
use Azuriom\Plugin\DuneRp\Models\House;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AdminEventController extends Controller
{
    /**
     * Display a listing of events.
     */
    public function index(Request $request)
    {
        $query = RpEvent::with(['organizer', 'organizerHouse']);

        // Filtres
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('event_type')) {
            $query->where('event_type', $request->event_type);
        }

        if ($request->filled('organizer_house_id')) {
            $query->where('organizer_house_id', $request->organizer_house_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('event_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('event_date', '<=', $request->date_to);
        }

        // Tri par défaut : événements à venir en premier
        $events = $query->orderByRaw("
            CASE 
                WHEN status = 'ongoing' THEN 1
                WHEN status = 'planned' AND event_date > NOW() THEN 2
                WHEN status = 'completed' THEN 3
                ELSE 4
            END
        ")
        ->orderBy('event_date', 'desc')
        ->paginate(20);

        $houses = House::orderBy('name')->get();

        $stats = [
            'total' => RpEvent::count(),
            'planned' => RpEvent::where('status', 'planned')->count(),
            'ongoing' => RpEvent::where('status', 'ongoing')->count(),
            'completed' => RpEvent::where('status', 'completed')->count(),
            'cancelled' => RpEvent::where('status', 'cancelled')->count(),
        ];

        return view('dune-rp::admin.events.index', compact('events', 'houses', 'stats'));
    }

    /**
     * Show the form for creating a new event.
     */
    public function create()
    {
        $users = User::orderBy('name')->get();
        $houses = House::where('is_active', true)->orderBy('name')->get();

        return view('dune-rp::admin.events.create', compact('users', 'houses'));
    }

    /**
     * Store a newly created event.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'organizer_id' => ['required', 'exists:users,id'],
            'organizer_house_id' => ['nullable', 'exists:dune_rp_houses,id'],
            'event_date' => ['required', 'date'],
            'location' => ['nullable', 'string', 'max:255'],
            'max_participants' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'spice_cost' => ['nullable', 'numeric', 'min:0', 'max:10000'],
            'reward_spice' => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'event_type' => ['required', Rule::in(array_keys(RpEvent::EVENT_TYPES))],
            'status' => ['required', Rule::in(array_keys(RpEvent::STATUSES))],
            'is_public' => ['boolean'],
        ]);

        $event = RpEvent::create($request->validated());

        return redirect()->route('dune-rp.admin.events.index')
                       ->with('success', trans('dune-rp::admin.events.created'));
    }

    /**
     * Display the specified event.
     */
    public function show(RpEvent $event)
    {
        $event->load(['organizer', 'organizerHouse', 'spiceTransactions']);

        $stats = [
            'total_cost' => $event->spice_cost,
            'total_reward' => $event->reward_spice,
            'related_transactions' => $event->spiceTransactions->count(),
            'transaction_volume' => $event->spiceTransactions->sum('amount'),
        ];

        return view('dune-rp::admin.events.show', compact('event', 'stats'));
    }

    /**
     * Show the form for editing the specified event.
     */
    public function edit(RpEvent $event)
    {
        $users = User::orderBy('name')->get();
        $houses = House::where('is_active', true)->orderBy('name')->get();

        return view('dune-rp::admin.events.edit', compact('event', 'users', 'houses'));
    }

    /**
     * Update the specified event.
     */
    public function update(Request $request, RpEvent $event)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'organizer_id' => ['required', 'exists:users,id'],
            'organizer_house_id' => ['nullable', 'exists:dune_rp_houses,id'],
            'event_date' => ['required', 'date'],
            'location' => ['nullable', 'string', 'max:255'],
            'max_participants' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'spice_cost' => ['nullable', 'numeric', 'min:0', 'max:10000'],
            'reward_spice' => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'event_type' => ['required', Rule::in(array_keys(RpEvent::EVENT_TYPES))],
            'status' => ['required', Rule::in(array_keys(RpEvent::STATUSES))],
            'is_public' => ['boolean'],
        ]);

        $event->update($request->validated());

        return redirect()->route('dune-rp.admin.events.index')
                       ->with('success', trans('dune-rp::admin.events.updated'));
    }

    /**
     * Remove the specified event.
     */
    public function destroy(RpEvent $event)
    {
        $event->delete();

        return redirect()->route('dune-rp.admin.events.index')
                       ->with('success', trans('dune-rp::admin.events.deleted'));
    }

    /**
     * Mark an event as completed and distribute rewards.
     */
    public function complete(Request $request, RpEvent $event)
    {
        if ($event->status === 'completed') {
            return back()->with('info', 'Event is already completed');
        }

        $request->validate([
            'reward_distribution' => ['nullable', 'array'],
            'reward_distribution.*.house_id' => ['exists:dune_rp_houses,id'],
            'reward_distribution.*.amount' => ['numeric', 'min:0', 'max:100000'],
            'completion_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($event, $request) {
            $event->update([
                'status' => 'completed',
                'completion_notes' => $request->completion_notes,
            ]);

            // Distribuer les récompenses
            if ($request->filled('reward_distribution')) {
                foreach ($request->reward_distribution as $reward) {
                    if ($reward['amount'] > 0) {
                        $house = House::find($reward['house_id']);
                        $house->addSpice(
                            $reward['amount'], 
                            'Event completion reward: ' . $event->title
                        );
                    }
                }
            } elseif ($event->reward_spice > 0 && $event->organizerHouse) {
                // Récompense par défaut à la maison organisatrice
                $event->organizerHouse->addSpice(
                    $event->reward_spice, 
                    'Event completion reward: ' . $event->title
                );
            }
        });

        return back()->with('success', trans('dune-rp::admin.events.completed'));
    }

    /**
     * Cancel an event.
     */
    public function cancel(Request $request, RpEvent $event)
    {
        if ($event->status === 'completed') {
            return back()->withErrors(['status' => 'Completed events cannot be cancelled']);
        }

        $request->validate([
            'cancellation_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $event->update([
            'status' => 'cancelled',
            'cancellation_reason' => $request->cancellation_reason,
        ]);

        // Rembourser les coûts d'épice si nécessaire
        if ($event->spice_cost > 0 && $event->organizerHouse) {
            $event->organizerHouse->addSpice(
                $event->spice_cost, 
                'Event cancellation refund: ' . $event->title
            );
        }

        return back()->with('success', trans('dune-rp::admin.events.cancelled'));
    }

    /**
     * Bulk actions for events.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => ['required', 'in:complete,cancel,delete,make_public,make_private'],
            'events' => ['required', 'array'],
            'events.*' => ['exists:dune_rp_events,id'],
        ]);

        $events = RpEvent::whereIn('id', $request->events)->get();
        $count = 0;

        foreach ($events as $event) {
            switch ($request->action) {
                case 'complete':
                    if ($event->status !== 'completed') {
                        $event->update(['status' => 'completed']);
                        if ($event->reward_spice > 0 && $event->organizerHouse) {
                            $event->organizerHouse->addSpice($event->reward_spice, 'Bulk completion reward');
                        }
                        $count++;
                    }
                    break;
                
                case 'cancel':
                    if ($event->status !== 'completed' && $event->status !== 'cancelled') {
                        $event->update(['status' => 'cancelled']);
                        $count++;
                    }
                    break;
                
                case 'delete':
                    $event->delete();
                    $count++;
                    break;
                
                case 'make_public':
                    $event->update(['is_public' => true]);
                    $count++;
                    break;
                
                case 'make_private':
                    $event->update(['is_public' => false]);
                    $count++;
                    break;
            }
        }

        return back()->with('success', "{$count} events processed");
    }

    /**
     * Event statistics and analytics.
     */
    public function analytics()
    {
        $analytics = [
            'events_by_type' => RpEvent::selectRaw('event_type, COUNT(*) as count')
                                     ->groupBy('event_type')
                                     ->pluck('count', 'event_type'),
            
            'events_by_status' => RpEvent::selectRaw('status, COUNT(*) as count')
                                        ->groupBy('status')
                                        ->pluck('count', 'status'),
            
            'events_by_month' => RpEvent::selectRaw('DATE_FORMAT(event_date, "%Y-%m") as month, COUNT(*) as count')
                                       ->where('event_date', '>=', now()->subYear())
                                       ->groupBy('month')
                                       ->orderBy('month')
                                       ->pluck('count', 'month'),
            
            'top_organizers' => RpEvent::with('organizer')
                                     ->selectRaw('organizer_id, COUNT(*) as event_count')
                                     ->groupBy('organizer_id')
                                     ->orderByDesc('event_count')
                                     ->limit(10)
                                     ->get(),
            
            'top_organizing_houses' => RpEvent::with('organizerHouse')
                                            ->selectRaw('organizer_house_id, COUNT(*) as event_count')
                                            ->whereNotNull('organizer_house_id')
                                            ->groupBy('organizer_house_id')
                                            ->orderByDesc('event_count')
                                            ->limit(10)
                                            ->get(),
            
            'spice_economics' => [
                'total_rewards_distributed' => RpEvent::where('status', 'completed')->sum('reward_spice'),
                'total_costs_collected' => RpEvent::where('status', 'completed')->sum('spice_cost'),
                'average_event_cost' => RpEvent::where('spice_cost', '>', 0)->avg('spice_cost'),
                'average_event_reward' => RpEvent::where('reward_spice', '>', 0)->avg('reward_spice'),
            ],
        ];

        return view('dune-rp::admin.events.analytics', compact('analytics'));
    }
}
