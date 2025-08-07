<?php

namespace Azuriom\Plugin\DuneRp\Controllers;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Plugin\DuneRp\Models\RpEvent;
use Azuriom\Plugin\DuneRp\Models\House;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    /**
     * Display all public events.
     */
    public function index(Request $request)
    {
        $query = RpEvent::where('is_public', true)
                       ->with(['organizer', 'organizerHouse']);

        // Filtres
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('event_type')) {
            $query->where('event_type', $request->event_type);
        }

        if ($request->filled('house_id')) {
            $query->where('organizer_house_id', $request->house_id);
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
        ->orderBy('event_date', 'asc')
        ->paginate(15);

        $houses = House::where('is_active', true)->orderBy('name')->get();

        return view('dune-rp::events.index', compact('events', 'houses'));
    }

    /**
     * Display a specific event.
     */
    public function show(RpEvent $event)
    {
        if (!$event->is_public) {
            abort(404);
        }

        $event->load(['organizer', 'organizerHouse', 'spiceTransactions']);

        return view('dune-rp::events.show', compact('event'));
    }

    /**
     * Show event creation form.
     */
    public function create()
    {
        $this->authorize('create-event'); // Middleware custom à créer si besoin

        $houses = House::where('is_active', true)->orderBy('name')->get();

        return view('dune-rp::events.create', compact('houses'));
    }

    /**
     * Store a newly created event.
     */
    public function store(Request $request)
    {
        $this->authorize('create-event');

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'organizer_house_id' => ['nullable', 'exists:dune_rp_houses,id'],
            'event_date' => ['required', 'date', 'after:now'],
            'location' => ['nullable', 'string', 'max:255'],
            'max_participants' => ['nullable', 'integer', 'min:1', 'max:100'],
            'spice_cost' => ['nullable', 'numeric', 'min:0', 'max:1000'],
            'reward_spice' => ['nullable', 'numeric', 'min:0', 'max:10000'],
            'event_type' => ['required', Rule::in(array_keys(RpEvent::EVENT_TYPES))],
            'is_public' => ['boolean'],
        ]);

        $event = new RpEvent($request->validated());
        $event->organizer_id = auth()->id();
        $event->status = 'planned';

        $event->save();

        return redirect()->route('dune-rp.events.show', $event)
                       ->with('success', trans('dune-rp::messages.events.created'));
    }

    /**
     * Show event edit form.
     */
    public function edit(RpEvent $event)
    {
        // Seul l'organisateur ou un admin peut éditer
        if ($event->organizer_id !== auth()->id() && !auth()->user()->can('dune-rp.events.manage')) {
            abort(403);
        }

        $houses = House::where('is_active', true)->orderBy('name')->get();

        return view('dune-rp::events.edit', compact('event', 'houses'));
    }

    /**
     * Update the event.
     */
    public function update(Request $request, RpEvent $event)
    {
        if ($event->organizer_id !== auth()->id() && !auth()->user()->can('dune-rp.events.manage')) {
            abort(403);
        }

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'organizer_house_id' => ['nullable', 'exists:dune_rp_houses,id'],
            'event_date' => ['required', 'date'],
            'location' => ['nullable', 'string', 'max:255'],
            'max_participants' => ['nullable', 'integer', 'min:1', 'max:100'],
            'spice_cost' => ['nullable', 'numeric', 'min:0', 'max:1000'],
            'reward_spice' => ['nullable', 'numeric', 'min:0', 'max:10000'],
            'event_type' => ['required', Rule::in(array_keys(RpEvent::EVENT_TYPES))],
            'is_public' => ['boolean'],
        ]);

        $event->update($request->validated());

        return redirect()->route('dune-rp.events.show', $event)
                       ->with('success', trans('dune-rp::messages.events.updated'));
    }

    /**
     * Join an event.
     */
    public function join(RpEvent $event)
    {
        $user = auth()->user();
        $character = $user->characters()->where('is_approved', true)->first();

        if (!$character) {
            return back()->with('error', trans('dune-rp::messages.events.no_character'));
        }

        if (!$event->canUserParticipate($user)) {
            return back()->with('error', trans('dune-rp::messages.events.cannot_participate'));
        }

        // Déduire le coût en épice si nécessaire
        if ($event->spice_cost > 0 && $character->house) {
            if (!$character->house->removeSpice($event->spice_cost, 'Participation événement: ' . $event->title)) {
                return back()->with('error', trans('dune-rp::messages.events.insufficient_spice'));
            }
        }

        // Ici vous pourriez avoir une table de participants
        // EventParticipant::create(['event_id' => $event->id, 'user_id' => $user->id]);

        return back()->with('success', trans('dune-rp::messages.events.joined'));
    }

    /**
     * Display upcoming events.
     */
    public function upcoming()
    {
        $events = RpEvent::where('is_public', true)
                        ->where('status', 'planned')
                        ->where('event_date', '>', now())
                        ->with(['organizer', 'organizerHouse'])
                        ->orderBy('event_date')
                        ->paginate(12);

        return view('dune-rp::events.upcoming', compact('events'));
    }

    /**
     * Display event calendar.
     */
    public function calendar()
    {
        $events = RpEvent::where('is_public', true)
                        ->where('event_date', '>=', now()->startOfMonth())
                        ->where('event_date', '<=', now()->endOfMonth()->addMonths(2))
                        ->with(['organizer', 'organizerHouse'])
                        ->orderBy('event_date')
                        ->get();

        return view('dune-rp::events.calendar', compact('events'));
    }
}
