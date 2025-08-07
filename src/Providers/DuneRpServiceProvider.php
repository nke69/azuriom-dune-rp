<?php

namespace Azuriom\Plugin\DuneRp\Providers;

use Azuriom\Extensions\Plugin\BasePluginServiceProvider;
use Azuriom\Models\Permission;
use Azuriom\Plugin\DuneRp\Models\House;
use Azuriom\Plugin\DuneRp\Models\Character;
use Azuriom\Plugin\DuneRp\Models\SpiceTransaction;
use Azuriom\Plugin\DuneRp\Models\RpEvent;
use Azuriom\Models\ActionLog;

class DuneRpServiceProvider extends BasePluginServiceProvider
{
    /**
     * Register any plugin services.
     */
    public function register(): void
    {
        $this->registerMiddlewares();
    }

    /**
     * Bootstrap any plugin services.
     */
    public function boot(): void
    {
        $this->loadViews();
        $this->loadTranslations();
        $this->loadMigrations();
        
        $this->registerRouteDescriptions();
        $this->registerUserNavigation();
        $this->registerAdminNavigation();
        $this->registerPermissions();
        $this->registerLogModels();
    }

    /**
     * Returns the routes that should be able to be added to the navbar.
     */
    protected function routeDescriptions(): array
    {
        return [
            'dune-rp.index' => trans('dune-rp::messages.nav.home'),
            'dune-rp.houses.index' => trans('dune-rp::messages.nav.houses'),
            'dune-rp.characters.index' => trans('dune-rp::messages.nav.characters'),
            'dune-rp.events.index' => trans('dune-rp::messages.nav.events'),
        ];
    }

    /**
     * Return the user navigations routes to register in the user menu.
     */
    protected function userNavigation(): array
    {
        return [
            'dune-rp' => [
                'route' => 'dune-rp.characters.my',
                'name' => trans('dune-rp::messages.nav.my_character'),
                'icon' => 'bi bi-person-badge',
            ],
        ];
    }

    /**
     * Return the admin navigations routes to register in the dashboard.
     */
    protected function adminNavigation(): array
    {
        return [
            'dune-rp' => [
                'name' => trans('dune-rp::admin.nav.title'),
                'type' => 'dropdown',
                'icon' => 'bi bi-globe-americas',
                'route' => 'dune-rp.admin.*',
                'items' => [
                    'dune-rp.admin.houses.index' => [
                        'name' => trans('dune-rp::admin.nav.houses'),
                        'permission' => 'dune-rp.houses.manage',
                    ],
                    'dune-rp.admin.characters.index' => [
                        'name' => trans('dune-rp::admin.nav.characters'),
                        'permission' => 'dune-rp.characters.manage',
                    ],
                    'dune-rp.admin.events.index' => [
                        'name' => trans('dune-rp::admin.nav.events'),
                        'permission' => 'dune-rp.events.manage',
                    ],
                ],
            ],
        ];
    }

    /**
     * Register the plugin permissions.
     */
    protected function registerPermissions(): void
    {
        Permission::registerPermissions([
            'dune-rp.houses.manage' => 'dune-rp::admin.permissions.houses.manage',
            'dune-rp.characters.manage' => 'dune-rp::admin.permissions.characters.manage',
            'dune-rp.events.manage' => 'dune-rp::admin.permissions.events.manage',
        ]);
    }

    /**
     * Register the log models.
     */
    protected function registerLogModels(): void
    {
        ActionLog::registerLogModels([
            House::class,
            Character::class,
            SpiceTransaction::class,
            RpEvent::class,
        ], 'dune-rp::admin.logs');
    }
}
