<?php

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
        Route::middleware('web')->group(function () {
            $this->loadRoutesFrom(plugin_path('dune-rp/routes/web.php'));
        });

        Route::prefix('admin')->middleware('admin-access')->group(function () {
            $this->loadRoutesFrom(plugin_path('dune-rp/routes/admin.php'));
        });

        Route::prefix('api')->middleware('api')->group(function () {
            $this->loadRoutesFrom(plugin_path('dune-rp/routes/api.php'));
        });
    }
}
