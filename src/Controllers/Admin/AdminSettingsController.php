<?php

namespace Azuriom\Plugin\DuneRp\Controllers\Admin;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Models\Setting;
use Illuminate\Http\Request;

class AdminSettingsController extends Controller
{
    /**
     * Display the plugin settings.
     */
    public function index()
    {
        $settings = [
            // Paramètres généraux
            'dune_rp_enabled' => setting('dune_rp_enabled', true),
            'dune_rp_maintenance_mode' => setting('dune_rp_maintenance_mode', false),
            
            // Paramètres des personnages
            'character_approval_required' => setting('dune_rp.character_approval_required', true),
            'max_characters_per_user' => setting('dune_rp.max_characters_per_user', 1),
            'character_biography_required' => setting('dune_rp.character_biography_required', false),
            'character_max_abilities' => setting('dune_rp.character_max_abilities', 5),
            
            // Paramètres des maisons
            'auto_assign_influence' => setting('dune_rp.auto_assign_influence', true),
            'default_spice_amount' => setting('dune_rp.default_spice_amount', 1000),
            'max_house_members' => setting('dune_rp.max_house_members', 0), // 0 = illimité
            
            // Paramètres de l'épice
            'spice_symbol' => setting('dune_rp.spice_symbol', '⚡'),
            'enable_spice_transfers' => setting('dune_rp.enable_spice_transfers', false),
            'daily_spice_bonus' => setting('dune_rp.daily_spice_bonus', 0),
            'spice_decay_enabled' => setting('dune_rp.spice_decay_enabled', false),
            'spice_decay_rate' => setting('dune_rp.spice_decay_rate', 0.01), // 1% par jour
            
            // Paramètres des événements
            'event_creation_permission' => setting('dune_rp.event_creation_permission', 'all'), // all, leaders, admins
            'event_approval_required' => setting('dune_rp.event_approval_required', false),
            'max_event_participants' => setting('dune_rp.max_event_participants', 100),
            'auto_complete_events' => setting('dune_rp.auto_complete_events', false),
            
            // Paramètres d'affichage
            'show_spice_reserves' => setting('dune_rp.show_spice_reserves', true),
            'show_house_rankings' => setting('dune_rp.show_house_rankings', true),
            'items_per_page' => setting('dune_rp.items_per_page', 20),
            
            // Notifications
            'notify_character_approved' => setting('dune_rp.notify_character_approved', true),
            'notify_house_events' => setting('dune_rp.notify_house_events', true),
            'notify_spice_changes' => setting('dune_rp.notify_spice_changes', false),
        ];

        return view('dune-rp::admin.settings.index', compact('settings'));
    }

    /**
     * Update the plugin settings.
     */
    public function update(Request $request)
    {
        $request->validate([
            // Général
            'dune_rp_enabled' => ['boolean'],
            'dune_rp_maintenance_mode' => ['boolean'],
            
            // Personnages
            'character_approval_required' => ['boolean'],
            'max_characters_per_user' => ['integer', 'min:1', 'max:10'],
            'character_biography_required' => ['boolean'],
            'character_max_abilities' => ['integer', 'min:1', 'max:10'],
            
            // Maisons
            'auto_assign_influence' => ['boolean'],
            'default_spice_amount' => ['numeric', 'min:0', 'max:1000000'],
            'max_house_members' => ['integer', 'min:0', 'max:1000'],
            
            // Épice
            'spice_symbol' => ['string', 'max:5'],
            'enable_spice_transfers' => ['boolean'],
            'daily_spice_bonus' => ['numeric', 'min:0', 'max:10000'],
            'spice_decay_enabled' => ['boolean'],
            'spice_decay_rate' => ['numeric', 'min:0', 'max:1'],
            
            // Événements
            'event_creation_permission' => ['in:all,leaders,admins'],
            'event_approval_required' => ['boolean'],
            'max_event_participants' => ['integer', 'min:1', 'max:10000'],
            'auto_complete_events' => ['boolean'],
            
            // Affichage
            'show_spice_reserves' => ['boolean'],
            'show_house_rankings' => ['boolean'],
            'items_per_page' => ['integer', 'min:5', 'max:100'],
            
            // Notifications
            'notify_character_approved' => ['boolean'],
            'notify_house_events' => ['boolean'],
            'notify_spice_changes' => ['boolean'],
        ]);

        foreach ($request->validated() as $key => $value) {
            if (str_starts_with($key, 'dune_rp_')) {
                Setting::updateSettings($key, $value);
            } else {
                Setting::updateSettings('dune_rp.' . $key, $value);
            }
        }

        return back()->with('success', trans('dune-rp::admin.settings.updated'));
    }

    /**
     * Reset settings to default.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'section' => ['required', 'in:all,general,characters,houses,spice,events,display,notifications'],
        ]);

        $defaultSettings = [
            'general' => [
                'dune_rp_enabled' => true,
                'dune_rp_maintenance_mode' => false,
            ],
            'characters' => [
                'dune_rp.character_approval_required' => true,
                'dune_rp.max_characters_per_user' => 1,
                'dune_rp.character_biography_required' => false,
                'dune_rp.character_max_abilities' => 5,
            ],
            'houses' => [
                'dune_rp.auto_assign_influence' => true,
                'dune_rp.default_spice_amount' => 1000,
                'dune_rp.max_house_members' => 0,
            ],
            'spice' => [
                'dune_rp.spice_symbol' => '⚡',
                'dune_rp.enable_spice_transfers' => false,
                'dune_rp.daily_spice_bonus' => 0,
                'dune_rp.spice_decay_enabled' => false,
                'dune_rp.spice_decay_rate' => 0.01,
            ],
            'events' => [
                'dune_rp.event_creation_permission' => 'all',
                'dune_rp.event_approval_required' => false,
                'dune_rp.max_event_participants' => 100,
                'dune_rp.auto_complete_events' => false,
            ],
            'display' => [
                'dune_rp.show_spice_reserves' => true,
                'dune_rp.show_house_rankings' => true,
                'dune_rp.items_per_page' => 20,
            ],
            'notifications' => [
                'dune_rp.notify_character_approved' => true,
                'dune_rp.notify_house_events' => true,
                'dune_rp.notify_spice_changes' => false,
            ],
        ];

        if ($request->section === 'all') {
            foreach ($defaultSettings as $sectionSettings) {
                foreach ($sectionSettings as $key => $value) {
                    Setting::updateSettings($key, $value);
                }
            }
        } else {
            foreach ($defaultSettings[$request->section] as $key => $value) {
                Setting::updateSettings($key, $value);
            }
        }

        return back()->with('success', 'Settings reset to default values');
    }

    /**
     * Import settings from JSON file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'settings_file' => ['required', 'file', 'mimes:json', 'max:1024'], // 1MB max
        ]);

        try {
            $content = file_get_contents($request->file('settings_file')->getRealPath());
            $settings = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withErrors(['settings_file' => 'Invalid JSON file']);
            }

            $imported = 0;
            foreach ($settings as $key => $value) {
                if (str_starts_with($key, 'dune_rp')) {
                    Setting::updateSettings($key, $value);
                    $imported++;
                }
            }

            return back()->with('success', "{$imported} settings imported successfully");

        } catch (\Exception $e) {
            return back()->withErrors(['settings_file' => 'Error reading file: ' . $e->getMessage()]);
        }
    }

    /**
     * Export settings to JSON file.
     */
    public function export()
    {
        $settings = Setting::where('name', 'like', 'dune_rp%')->pluck('value', 'name');

        $filename = 'dune_rp_settings_' . now()->format('Y-m-d_H-i-s') . '.json';

        return response()->json($settings, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Clear all plugin cache.
     */
    public function clearCache()
    {
        // Nettoyer le cache Laravel
        \Cache::tags(['dune-rp'])->flush();
        
        // Autres actions de nettoyage si nécessaire
        \Artisan::call('view:clear');
        \Artisan::call('config:clear');

        return back()->with('success', 'Plugin cache cleared successfully');
    }
}
