<?php

use Illuminate\Support\Facades\Route;
use Azuriom\Plugin\DuneRp\Models\House;
use Azuriom\Plugin\DuneRp\Models\Character;
use Azuriom\Plugin\DuneRp\Models\RpEvent;

/*
|--------------------------------------------------------------------------
| Dune RP API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('api')->group(function () {
    
    // ===== API PUBLIQUE =====
    
    // Statistiques générales
    Route::get('/stats', function () {
        return response()->json([
            'houses' => House::where('is_active', true)->count(),
            'characters' => Character::where('is_approved', true)->count(),
            'total_spice' => House::where('is_active', true)->sum('spice_reserves'),
            'active_events' => RpEvent::where('status', 'ongoing')->count(),
        ]);
    })->name('dune-rp.api.stats');
    
    // Liste des maisons
    Route::get('/houses', function () {
        $houses = House::where('is_active', true)
                      ->select(['id', 'name', 'motto', 'homeworld', 'color', 'influence_points', 'spice_reserves'])
                      ->withCount(['characters as members_count' => function($query) {
                          $query->where('is_approved', true)->where('status', 'alive');
                      }])
                      ->orderByDesc('influence_points')
                      ->get();
        
        return response()->json($houses);
    })->name('dune-rp.api.houses');
    
    // Détails d'une maison
    Route::get('/houses/{house}', function (House $house) {
        if (!$house->is_active) {
            return response()->json(['error' => 'House not found'], 404);
        }
        
        $house->load(['leader:id,name']);
        $house->loadCount(['characters as members_count' => function($query) {
            $query->where('is_approved', true)->where('status', 'alive');
        }]);
        
        return response()->json($house);
    })->name('dune-rp.api.houses.show');
    
    // Liste des personnages publics
    Route::get('/characters', function () {
        $characters = Character::where('is_public', true)
                              ->where('is_approved', true)
                              ->select(['id', 'name', 'title', 'house_id', 'status', 'created_at'])
                              ->with(['house:id,name,color', 'user:id,name'])
                              ->latest()
                              ->paginate(20);
        
        return response()->json($characters);
    })->name('dune-rp.api.characters');
    
    // Événements publics
    Route::get('/events', function () {
        $events = RpEvent::where('is_public', true)
                        ->select(['id', 'title', 'event_date', 'location', 'event_type', 'status', 'organizer_house_id'])
                        ->with(['organizerHouse:id,name,color'])
                        ->orderBy('event_date')
                        ->paginate(20);
        
        return response()->json($events);
    })->name('dune-rp.api.events');
    
    // ===== API AUTHENTIFIÉE =====
    
    Route::middleware('auth:sanctum')->group(function () {
        
        // Personnage de l'utilisateur connecté
        Route::get('/my-character', function () {
            $character = Character::where('user_id', auth()->id())
                                 ->with(['house:id,name,color'])
                                 ->first();
            
            return response()->json($character);
        })->name('dune-rp.api.my-character');
        
        // Transfert d'épice (si implémenté)
        Route::post('/transfer-spice', function () {
            // Logique de transfert d'épice entre maisons
            return response()->json(['message' => 'Feature not implemented yet']);
        })->name('dune-rp.api.transfer-spice');
    });
});
