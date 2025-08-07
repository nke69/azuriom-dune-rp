<?php

namespace Azuriom\Plugin\DuneRp\Controllers;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Plugin\DuneRp\Models\Character;
use Azuriom\Plugin\DuneRp\Models\House;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CharacterController extends Controller
{
    /**
     * Display all public approved characters.
     */
    public function index(Request $request)
    {
        $query = Character::where('is_public', true)
                         ->where('is_approved', true)
                         ->with(['user', 'house']);

        // Filtres
        if ($request->filled('house_id')) {
            $query->where('house_id', $request->house_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($q) use ($request) {
                      $q->where('name', 'like', '%' . $request->search . '%');
                  });
        }

        $characters = $query->latest()->paginate(20);
        $houses = House::where('is_active', true)->orderBy('name')->get();

        return view('dune-rp::characters.index', compact('characters', 'houses'));
    }

    /**
     * Display a specific character profile.
     */
    public function show(Character $character)
    {
        // Vérifier que le personnage est public et approuvé
        if (!$character->is_public || !$character->is_approved) {
            abort(404);
        }

        $character->load(['user', 'house']);

        return view('dune-rp::characters.show', compact('character'));
    }

    /**
     * Display current user's character.
     */
    public function my()
    {
        $character = Character::where('user_id', auth()->id())->first();

        if (!$character) {
            return redirect()->route('dune-rp.characters.create')
                           ->with('info', trans('dune-rp::messages.characters.create_first'));
        }

        $character->load('house');

        return view('dune-rp::characters.my', compact('character'));
    }

    /**
     * Show character creation form.
     */
    public function create()
    {
        // Vérifier que l'utilisateur n'a pas déjà un personnage
        $existingCharacter = Character::where('user_id', auth()->id())->first();

        if ($existingCharacter) {
            return redirect()->route('dune-rp.characters.my')
                           ->with('error', trans('dune-rp::messages.characters.already_exists'));
        }

        $houses = House::where('is_active', true)->orderBy('name')->get();

        return view('dune-rp::characters.create', compact('houses'));
    }

    /**
     * Store a newly created character.
     */
    public function store(Request $request)
    {
        // Vérifier que l'utilisateur n'a pas déjà un personnage
        $existingCharacter = Character::where('user_id', auth()->id())->first();

        if ($existingCharacter) {
            return redirect()->route('dune-rp.characters.my')
                           ->with('error', trans('dune-rp::messages.characters.already_exists'));
        }

        $request->validate([
            'name' => ['required', 'string', 'max:100', 'min:2'],
            'title' => ['nullable', 'string', 'max:100'],
            'house_id' => ['nullable', 'exists:dune_rp_houses,id'],
            'biography' => ['nullable', 'string', 'max:5000'],
            'birthworld' => ['nullable', 'string', 'max:100'],
            'age' => ['nullable', 'integer', 'min:1', 'max:500'],
            'special_abilities' => ['nullable', 'array', 'max:5'],
            'special_abilities.*' => ['string', Rule::in(array_keys(Character::SPECIAL_ABILITIES))],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'is_public' => ['boolean'],
        ]);

        $character = new Character($request->validated());
        $character->user_id = auth()->id();
        $character->is_approved = false; // Nécessite approbation admin

        // Upload de l'avatar si présent
        if ($request->hasFile('avatar')) {
            $character->storeImage($request->file('avatar'));
        }

        $character->save();

        return redirect()->route('dune-rp.characters.my')
                       ->with('success', trans('dune-rp::messages.characters.created_pending_approval'));
    }

    /**
     * Show character edit form.
     */
    public function edit()
    {
        $character = Character::where('user_id', auth()->id())->firstOrFail();
        $houses = House::where('is_active', true)->orderBy('name')->get();

        return view('dune-rp::characters.edit', compact('character', 'houses'));
    }

    /**
     * Update the user's character.
     */
    public function update(Request $request)
    {
        $character = Character::where('user_id', auth()->id())->firstOrFail();

        $request->validate([
            'name' => ['required', 'string', 'max:100', 'min:2'],
            'title' => ['nullable', 'string', 'max:100'],
            'house_id' => ['nullable', 'exists:dune_rp_houses,id'],
            'biography' => ['nullable', 'string', 'max:5000'],
            'birthworld' => ['nullable', 'string', 'max:100'],
            'age' => ['nullable', 'integer', 'min:1', 'max:500'],
            'special_abilities' => ['nullable', 'array', 'max:5'],
            'special_abilities.*' => ['string', Rule::in(array_keys(Character::SPECIAL_ABILITIES))],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'is_public' => ['boolean'],
        ]);

        // Si la maison change, remettre en attente d'approbation
        if ($character->house_id != $request->house_id) {
            $character->is_approved = false;
        }

        $character->fill($request->validated());

        // Upload nouvel avatar si présent
        if ($request->hasFile('avatar')) {
            $character->storeImage($request->file('avatar'));
        }

        $character->save();

        $message = $character->house_id != $request->house_id 
            ? trans('dune-rp::messages.characters.updated_pending_approval')
            : trans('dune-rp::messages.characters.updated');

        return redirect()->route('dune-rp.characters.my')
                       ->with('success', $message);
    }

    /**
     * Display character gallery.
     */
    public function gallery()
    {
        $characters = Character::where('is_public', true)
                              ->where('is_approved', true)
                              ->whereNotNull('avatar_url')
                              ->with(['user', 'house'])
                              ->latest()
                              ->paginate(24);

        return view('dune-rp::characters.gallery', compact('characters'));
    }

    /**
     * Display characters by house.
     */
    public function byHouse(House $house)
    {
        if (!$house->is_active) {
            abort(404);
        }

        $characters = $house->characters()
                           ->where('is_public', true)
                           ->where('is_approved', true)
                           ->where('status', 'alive')
                           ->with('user')
                           ->latest()
                           ->paginate(20);

        return view('dune-rp::characters.by-house', compact('house', 'characters'));
    }
}
