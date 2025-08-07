<?php

namespace Azuriom\Plugin\DuneRp\Providers;

use Azuriom\Extensions\Plugin\BaseRouteServiceProvider;

class RouteServiceProvider extends BaseRouteServiceProvider
{
    /**
     * Define the plugin routes.
     */
    protected function loadRoutes(): void
    {
        $this->routes();
    }

    /**
     * Define the routes for the plugin.
     */
    protected function routes(): void
    {
        $this->loadRoutesFrom(plugin_path('DuneRp/routes/web.php'));
        $this->loadRoutesFrom(plugin_path('DuneRp/routes/admin.php'));
        $this->loadRoutesFrom(plugin_path('DuneRp/routes/api.php'));
    }
}
