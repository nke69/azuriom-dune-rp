<?php

use Azuriom\Plugin\DuneRp\Controllers\DuneRpController;
use Azuriom\Plugin\DuneRp\Controllers\HouseController;
use Azuriom\Plugin\DuneRp\Controllers\CharacterController;
use Azuriom\Plugin\DuneRp\Controllers\EventController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Dune RP Web Routes
|--------------------------------------------------------------------------
*/

Route::group(['middleware' => 'web'], function () {
    
    // ===== ROUTES PRINCIPALES =====
    Route::get('/', [DuneRpController::class, 'index'])->name('dune-rp.index');
    Route::get('/statistics', [DuneRpController::class, 'statistics'])->name('dune-rp.statistics');
    Route::get('/lore', [DuneRpController::class, 'lore'])->name('dune-rp.lore');

    // ===== ROUTES DES MAISONS =====
    Route::prefix('houses')->name('dune-rp.houses.')->group(function () {
        Route::get('/', [HouseController::class, 'index'])->name('index');
        Route::get('/recruitment', [HouseController::class, 'recruitment'])->name('recruitment');
        Route::get('/compare', [HouseController::class, 'compare'])->name('compare');
        Route::get('/leaderboard', [HouseController::class, 'leaderboard'])->name('leaderboard');
        Route::get('/{house}', [HouseController::class, 'show'])->name('show');
    });

    // ===== ROUTES DES PERSONNAGES =====
    Route::prefix('characters')->name('dune-rp.characters.')->group(function () {
        // Routes publiques
        Route::get('/', [CharacterController::class, 'index'])->name('index');
        Route::get('/gallery', [CharacterController::class, 'gallery'])->name('gallery');
        Route::get('/house/{house}', [CharacterController::class, 'byHouse'])->name('by-house');
        Route::get('/{character}', [CharacterController::class, 'show'])->name('show');
        
        // Routes authentifiées
        Route::middleware('auth')->group(function () {
            Route::get('/my/character', [CharacterController::class, 'my'])->name('my');
            Route::get('/create/character', [CharacterController::class, 'create'])->name('create');
            Route::post('/create/character', [CharacterController::class, 'store'])->name('store');
            Route::get('/edit/character', [CharacterController::class, 'edit'])->name('edit');
            Route::put('/edit/character', [CharacterController::class, 'update'])->name('update');
        });
    });

    // ===== ROUTES DES ÉVÉNEMENTS =====
    Route::prefix('events')->name('dune-rp.events.')->group(function () {
        // Routes publiques
        Route::get('/', [EventController::class, 'index'])->name('index');
        Route::get('/upcoming', [EventController::class, 'upcoming'])->name('upcoming');
        Route::get('/calendar', [EventController::class, 'calendar'])->name('calendar');
        Route::get('/{event}', [EventController::class, 'show'])->name('show');
        
        // Routes authentifiées
        Route::middleware('auth')->group(function () {
            Route::get('/create/event', [EventController::class, 'create'])->name('create');
            Route::post('/create/event', [EventController::class, 'store'])->name('store');
            Route::get('/{event}/edit', [EventController::class, 'edit'])->name('edit');
            Route::put('/{event}', [EventController::class, 'update'])->name('update');
            Route::post('/{event}/join', [EventController::class, 'join'])->name('join');
        });
    });
});
