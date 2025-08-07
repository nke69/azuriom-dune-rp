<?php

use Azuriom\Plugin\DuneRp\Controllers\Admin\AdminHouseController;
use Azuriom\Plugin\DuneRp\Controllers\Admin\AdminCharacterController;
use Azuriom\Plugin\DuneRp\Controllers\Admin\AdminEventController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Dune RP Admin Routes
|--------------------------------------------------------------------------
| Ces routes sont automatiquement préfixées par 'admin/dune-rp'
| et nommées avec le préfixe 'admin.dune-rp.'
|--------------------------------------------------------------------------
*/

// Page d'accueil admin du plugin
Route::get('/', function() {
    return redirect()->route('admin.dune-rp.houses.index');
})->name('index');

// ===== GESTION DES MAISONS =====
Route::prefix('houses')->name('houses.')->group(function () {
    Route::get('/', [AdminHouseController::class, 'index'])->name('index')->middleware('can:dune-rp.houses.manage');
    Route::get('/create', [AdminHouseController::class, 'create'])->name('create')->middleware('can:dune-rp.houses.manage');
    Route::post('/', [AdminHouseController::class, 'store'])->name('store')->middleware('can:dune-rp.houses.manage');
    Route::get('/{house}', [AdminHouseController::class, 'show'])->name('show')->middleware('can:dune-rp.houses.manage');
    Route::get('/{house}/edit', [AdminHouseController::class, 'edit'])->name('edit')->middleware('can:dune-rp.houses.manage');
    Route::put('/{house}', [AdminHouseController::class, 'update'])->name('update')->middleware('can:dune-rp.houses.manage');
    Route::delete('/{house}', [AdminHouseController::class, 'destroy'])->name('destroy')->middleware('can:dune-rp.houses.manage');
    
    // Actions spéciales
    Route::post('/{house}/spice', [AdminHouseController::class, 'adjustSpice'])->name('adjust-spice')->middleware('can:dune-rp.houses.manage');
});

// ===== GESTION DES PERSONNAGES =====
Route::prefix('characters')->name('characters.')->group(function () {
    Route::get('/', [AdminCharacterController::class, 'index'])->name('index')->middleware('can:dune-rp.characters.manage');
    Route::get('/create', [AdminCharacterController::class, 'create'])->name('create')->middleware('can:dune-rp.characters.manage');
    Route::post('/', [AdminCharacterController::class, 'store'])->name('store')->middleware('can:dune-rp.characters.manage');
    Route::get('/pending', [AdminCharacterController::class, 'pending'])->name('pending')->middleware('can:dune-rp.characters.manage');
    Route::get('/{character}', [AdminCharacterController::class, 'show'])->name('show')->middleware('can:dune-rp.characters.manage');
    Route::get('/{character}/edit', [AdminCharacterController::class, 'edit'])->name('edit')->middleware('can:dune-rp.characters.manage');
    Route::put('/{character}', [AdminCharacterController::class, 'update'])->name('update')->middleware('can:dune-rp.characters.manage');
    Route::delete('/{character}', [AdminCharacterController::class, 'destroy'])->name('destroy')->middleware('can:dune-rp.characters.manage');
    
    // Actions d'approbation
    Route::post('/{character}/approve', [AdminCharacterController::class, 'approve'])->name('approve')->middleware('can:dune-rp.characters.manage');
    Route::post('/{character}/reject', [AdminCharacterController::class, 'reject'])->name('reject')->middleware('can:dune-rp.characters.manage');
});

// ===== GESTION DES ÉVÉNEMENTS =====
Route::prefix('events')->name('events.')->group(function () {
    Route::get('/', [AdminEventController::class, 'index'])->name('index')->middleware('can:dune-rp.events.manage');
    Route::get('/create', [AdminEventController::class, 'create'])->name('create')->middleware('can:dune-rp.events.manage');
    Route::post('/', [AdminEventController::class, 'store'])->name('store')->middleware('can:dune-rp.events.manage');
    Route::get('/{event}', [AdminEventController::class, 'show'])->name('show')->middleware('can:dune-rp.events.manage');
    Route::get('/{event}/edit', [AdminEventController::class, 'edit'])->name('edit')->middleware('can:dune-rp.events.manage');
    Route::put('/{event}', [AdminEventController::class, 'update'])->name('update')->middleware('can:dune-rp.events.manage');
    Route::delete('/{event}', [AdminEventController::class, 'destroy'])->name('destroy')->middleware('can:dune-rp.events.manage');
    
    // Actions de gestion
    Route::post('/{event}/complete', [AdminEventController::class, 'complete'])->name('complete')->middleware('can:dune-rp.events.manage');
    Route::post('/{event}/cancel', [AdminEventController::class, 'cancel'])->name('cancel')->middleware('can:dune-rp.events.manage');
});
