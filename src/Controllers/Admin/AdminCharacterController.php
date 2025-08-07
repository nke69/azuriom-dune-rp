<?php

namespace Azuriom\Plugin\DuneRp\Controllers\Admin;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Plugin\DuneRp\Models\Character;
use Azuriom\Plugin\DuneRp\Models\House;
use Illuminate\Http\Request;

class AdminCharacterController extends Controller
{
    /**
     * Display a listing of characters.
     */
    public function index(Request $request)
    {
        $query = Character::with(['user', 'house']);

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

        if ($request->filled('approval_status')) {
            $isApproved = $request->approval_status === 'approved';
            $query->where('is_approved', $isApproved);
        }

        $characters = $query->latest()->paginate(20);
        $houses = House::orderBy('name')->get();

        $stats = [
            'total' => Character::count(),
            'approved' => Character::where('is_approved', true)->count(),
            'pending' => Character::where('is_approved', false)->count(),
            'public' => Character::where('is_public', true)->count(),
        ];

        return view('dune-rp::admin.characters.index', compact('characters', 'houses', 'stats'));
    }

    /**
     * Display pending characters for approval.
     */
    public function pending()
    {
        $characters = Character::where('is_approved', false)
                              ->with(['user', 'house'])
                              ->latest()
                              ->paginate(15);

        return view('dune-rp::admin.characters.pending', compact('characters'));
    }

    /**
     * Display the specified character.
     */
    public function show(Character $character)
    {
        $character->load(['user', 'house']);

        return view('dune-rp::admin.characters.show', compact('character'));
    }

    /**
     * Show the form for editing the specified character.
     */
    public function edit(Character $character)
    {
        $houses = House::where('is_active', true)->orderBy('name')->get();
        
        return view('dune-rp::admin.characters.edit', compact('character', 'houses'));
    }

    /**
     * Update the specified character.
     */
    public function update(Request $request, Character $character)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'title' => ['nullable', 'string', 'max:100'],
            'house_id' => ['nullable', 'exists:dune_rp_houses,id'],
            'biography' => ['nullable', 'string', 'max:5000'],
            'birthworld' => ['nullable', 'string', 'max:100'],
            'age' => ['nullable', 'integer', 'min:1', 'max:500'],
            'status' => ['required', 'in:alive,missing,deceased,exiled'],
            'spice_addiction_level' => ['required', 'integer', 'min:0', 'max:4'],
            'special_abilities' => ['nullable', 'array'],
            'special_abilities.*' => ['string', 'max:100'],
            'is_public' => ['boolean'],
            'is_approved' => ['boolean'],
        ]);

        $character->fill($request->validated());
        $character->save();

        return redirect()->route('dune-rp.admin.characters.index')
                       ->with('success', trans('dune-rp::admin.characters.updated'));
    }

    /**
     * Remove the specified character.
     */
    public function destroy(Character $character)
    {
        $character->delete();

        return redirect()->route('dune-rp.admin.characters.index')
                       ->with('success', trans('dune-rp::admin.characters.deleted'));
    }

    /**
     * Approve a character.
     */
    public function approve(Character $character)
    {
        $character->update(['is_approved' => true]);

        // Optionnel: notification au joueur
        // Mail::to($character->user->email)->send(new CharacterApprovedMail($character));

        return back()->with('success', trans('dune-rp::admin.characters.approved'));
    }

    /**
     * Reject a character.
     */
    public function reject(Request $request, Character $character)
    {
        $request->validate([
            'rejection_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $character->update(['is_approved' => false]);

        // Optionnel: notification avec raison
        // Mail::to($character->user->email)->send(new CharacterRejectedMail($character, $request->rejection_reason));

        return back()->with('success', trans('dune-rp::admin.characters.rejected'));
    }

    /**
     * Bulk approve characters.
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'characters' => ['required', 'array'],
            'characters.*' => ['exists:dune_rp_characters,id'],
        ]);

        $count = Character::whereIn('id', $request->characters)
                         ->update(['is_approved' => true]);

        return back()->with('success', "{$count} characters approved");
    }

    /**
     * Bulk reject characters.
     */
    public function bulkReject(Request $request)
    {
        $request->validate([
            'characters' => ['required', 'array'],
            'characters.*' => ['exists:dune_rp_characters,id'],
            'rejection_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $count = Character::whereIn('id', $request->characters)
                         ->update(['is_approved' => false]);

        return back()->with('success', "{$count} characters rejected");
    }

    /**
     * Character statistics dashboard.
     */
    public function stats()
    {
        $stats = [
            'by_house' => Character::where('is_approved', true)
                                 ->with('house:id,name')
                                 ->get()
                                 ->groupBy('house.name')
                                 ->map->count(),
            'by_status' => Character::where('is_approved', true)
                                  ->selectRaw('status, COUNT(*) as count')
                                  ->groupBy('status')
                                  ->pluck('count', 'status'),
            'by_addiction' => Character::where('is_approved', true)
                                     ->selectRaw('spice_addiction_level, COUNT(*) as count')
                                     ->groupBy('spice_addiction_level')
                                     ->pluck('count', 'spice_addiction_level'),
            'creation_trend' => Character::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                                       ->where('created_at', '>=', now()->subDays(30))
                                       ->groupBy('date')
                                       ->orderBy('date')
                                       ->pluck('count', 'date'),
        ];

        return view('dune-rp::admin.characters.stats', compact('stats'));
    }
}
