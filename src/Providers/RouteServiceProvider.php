<?php
// ========================================
// src/Providers/RouteServiceProvider.php
// ========================================

namespace Azuriom\Plugin\DuneRp\Providers;

use Azuriom\Extensions\Plugin\BaseRouteServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends BaseRouteServiceProvider
{
    /**
     * Define the routes for the application.
     */
    public function loadRoutes(): void
    {
        // Routes publiques
        Route::prefix($this->plugin->id)
            ->middleware('web')
            ->name($this->plugin->id.'.')
            ->group(plugin_path($this->plugin->id.'/routes/web.php'));

        // Routes admin - IMPORTANT: le préfixe admin/dune-rp est ajouté ici
        Route::prefix('admin/'.$this->plugin->id)
            ->middleware('admin-access')
            ->name('admin.'.$this->plugin->id.'.')
            ->group(plugin_path($this->plugin->id.'/routes/admin.php'));

        // Routes API
        Route::prefix('api/'.$this->plugin->id)
            ->middleware('api')
            ->name('api.'.$this->plugin->id.'.')
            ->group(plugin_path($this->plugin->id.'/routes/api.php'));
    }
}
