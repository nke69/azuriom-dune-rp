<?php

use Azuriom\Plugin\DuneRp\Controllers\Admin\AdminHouseController;
use Azuriom\Plugin\DuneRp\Controllers\Admin\AdminCharacterController;
use Azuriom\Plugin\DuneRp\Controllers\Admin\AdminEventController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Dune RP Admin Routes
|--------------------------------------------------------------------------
*/

// ===== GESTION DES MAISONS =====
Route::prefix('houses')->name('dune-rp.admin.houses.')->middleware('can:dune-rp.houses.manage')->group(function () {
    Route::get('/', [AdminHouseController::class, 'index'])->name('index');
    Route::get('/create', [AdminHouseController::class, 'create'])->name('create');
    Route::post('/', [AdminHouseController::class, 'store'])->name('store');
    Route::get('/{house}', [AdminHouseController::class, 'show'])->name('show');
    Route::get('/{house}/edit', [AdminHouseController::class, 'edit'])->name('edit');
    Route::put('/{house}', [AdminHouseController::class, 'update'])->name('update');
    Route::delete('/{house}', [AdminHouseController::class, 'destroy'])->name('destroy');
    
    // Actions spéciales
    Route::post('/{house}/spice', [AdminHouseController::class, 'adjustSpice'])->name('adjust-spice');
});

// ===== GESTION DES PERSONNAGES =====
Route::prefix('characters')->name('dune-rp.admin.characters.')->middleware('can:dune-rp.characters.manage')->group(function () {
    Route::get('/', [AdminCharacterController::class, 'index'])->name('index');
    Route::get('/pending', [AdminCharacterController::class, 'pending'])->name('pending');
    Route::get('/{character}', [AdminCharacterController::class, 'show'])->name('show');
    Route::get('/{character}/edit', [AdminCharacterController::class, 'edit'])->name('edit');
    Route::put('/{character}', [AdminCharacterController::class, 'update'])->name('update');
    Route::delete('/{character}', [AdminCharacterController::class, 'destroy'])->name('destroy');
    
    // Actions d'approbation
    Route::post('/{character}/approve', [AdminCharacterController::class, 'approve'])->name('approve');
    Route::post('/{character}/reject', [AdminCharacterController::class, 'reject'])->name('reject');
});

// ===== GESTION DES ÉVÉNEMENTS =====
Route::prefix('events')->name('dune-rp.admin.events.')->middleware('can:dune-rp.events.manage')->group(function () {
    Route::get('/', [AdminEventController::class, 'index'])->name('index');
    Route::get('/create', [AdminEventController::class, 'create'])->name('create');
    Route::post('/', [AdminEventController::class, 'store'])->name('store');
    Route::get('/{event}', [AdminEventController::class, 'show'])->name('show');
    Route::get('/{event}/edit', [AdminEventController::class, 'edit'])->name('edit');
    Route::put('/{event}', [AdminEventController::class, 'update'])->name('update');
    Route::delete('/{event}', [AdminEventController::class, 'destroy'])->name('destroy');
    
    // Actions de gestion des événements
    Route::post('/{event}/complete', [AdminEventController::class, 'complete'])->name('complete');
    Route::post('/{event}/cancel', [AdminEventController::class, 'cancel'])->name('cancel');
});
