<?php

namespace Azuriom\Plugin\DuneRp\Controllers\Admin;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Models\User;
use Azuriom\Plugin\DuneRp\Models\House;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminHouseController extends Controller
{
    /**
     * Display a listing of houses.
     */
    public function index(Request $request)
    {
        $query = House::with(['leader', 'characters'])
                     ->withCount('characters');

        // Filtres
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('motto', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === '1');
        }

        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'influence':
                    $query->orderByDesc('influence_points');
                    break;
                case 'spice':
                    $query->orderByDesc('spice_reserves');
                    break;
                case 'members':
                    $query->orderByDesc('characters_count');
                    break;
                default:
                    $query->latest();
            }
        } else {
            $query->latest();
        }

        $houses = $query->paginate(15);

        $stats = [
            'total' => House::count(),
            'active' => House::where('is_active', true)->count(),
            'total_spice' => House::where('is_active', true)->sum('spice_reserves'),
            'total_influence' => House::where('is_active', true)->sum('influence_points'),
        ];

        return view('dune-rp::admin.houses.index', compact('houses', 'stats'));
    }

    /**
     * Show the form for creating a new house.
     */
    public function create()
    {
        $users = User::orderBy('name')->get();
        
        return view('dune-rp::admin.houses.create', compact('users'));
    }

    /**
     * Store a newly created house.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:dune_rp_houses'],
            'motto' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'leader_id' => ['nullable', 'exists:users,id'],
            'homeworld' => ['nullable', 'string', 'max:100'],
            'color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'spice_reserves' => ['nullable', 'numeric', 'min:0', 'max:999999999'],
            'influence_points' => ['nullable', 'integer', 'min:0', 'max:999999999'],
            'sigil' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['boolean'],
        ]);

        $house = new House($request->validated());

        if ($request->hasFile('sigil')) {
            $house->storeImage($request->file('sigil'));
        }

        $house->save();

        return redirect()->route('dune-rp.admin.houses.index')
                       ->with('success', trans('dune-rp::admin.houses.created'));
    }

    /**
     * Display the specified house.
     */
    public function show(House $house)
    {
        $house->load(['leader', 'characters.user', 'spiceTransactions', 'events']);

        $stats = [
            'total_members' => $house->characters()->count(),
            'active_members' => $house->characters()->where('is_approved', true)->where('status', 'alive')->count(),
            'pending_members' => $house->characters()->where('is_approved', false)->count(),
            'total_events' => $house->events()->count(),
            'completed_events' => $house->events()->where('status', 'completed')->count(),
            'total_transactions' => $house->spiceTransactions()->count(),
            'monthly_income' => $house->spiceTransactions()
                                    ->where('type', 'income')
                                    ->where('created_at', '>=', now()->startOfMonth())
                                    ->sum('amount'),
            'monthly_expenses' => $house->spiceTransactions()
                                     ->where('type', 'expense')
                                     ->where('created_at', '>=', now()->startOfMonth())
                                     ->sum('amount'),
        ];

        $recentTransactions = $house->spiceTransactions()
                                   ->with('relatedEvent')
                                   ->latest()
                                   ->limit(20)
                                   ->get();

        return view('dune-rp::admin.houses.show', compact('house', 'stats', 'recentTransactions'));
    }

    /**
     * Show the form for editing the specified house.
     */
    public function edit(House $house)
    {
        $users = User::orderBy('name')->get();
        
        return view('dune-rp::admin.houses.edit', compact('house', 'users'));
    }

    /**
     * Update the specified house.
     */
    public function update(Request $request, House $house)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('dune_rp_houses')->ignore($house->id)],
            'motto' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'leader_id' => ['nullable', 'exists:users,id'],
            'homeworld' => ['nullable', 'string', 'max:100'],
            'color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'spice_reserves' => ['nullable', 'numeric', 'min:0', 'max:999999999'],
            'influence_points' => ['nullable', 'integer', 'min:0', 'max:999999999'],
            'sigil' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['boolean'],
        ]);

        $house->fill($request->validated());

        if ($request->hasFile('sigil')) {
            $house->storeImage($request->file('sigil'));
        }

        $house->save();

        return redirect()->route('dune-rp.admin.houses.index')
                       ->with('success', trans('dune-rp::admin.houses.updated'));
    }

    /**
     * Remove the specified house.
     */
    public function destroy(House $house)
    {
        // Vérifier si la maison a des membres
        if ($house->characters()->exists()) {
            return back()->withErrors([
                'house' => 'Cannot delete house with existing members. Remove all members first.'
            ]);
        }

        $house->delete();

        return redirect()->route('dune-rp.admin.houses.index')
                       ->with('success', trans('dune-rp::admin.houses.deleted'));
    }

    /**
     * Adjust spice reserves for a house.
     */
    public function adjustSpice(Request $request, House $house)
    {
        $request->validate([
            'adjustment_type' => ['required', 'in:add,remove,set'],
            'amount' => ['required', 'numeric', 'min:0'],
            'reason' => ['required', 'string', 'max:255'],
        ]);

        $amount = $request->amount;
        $reason = 'Admin (' . auth()->user()->name . '): ' . $request->reason;

        switch ($request->adjustment_type) {
            case 'add':
                $house->addSpice($amount, $reason);
                $message = "Added {$amount} tons of spice to {$house->name}";
                break;
            
            case 'remove':
                if ($house->removeSpice($amount, $reason)) {
                    $message = "Removed {$amount} tons of spice from {$house->name}";
                } else {
                    return back()->withErrors(['amount' => 'Insufficient spice reserves']);
                }
                break;
            
            case 'set':
                $currentReserves = $house->spice_reserves;
                if ($amount > $currentReserves) {
                    $house->addSpice($amount - $currentReserves, $reason);
                } elseif ($amount < $currentReserves) {
                    $house->removeSpice($currentReserves - $amount, $reason);
                }
                $message = "Set {$house->name} spice reserves to {$amount} tons";
                break;
        }

        return back()->with('success', $message);
    }

    /**
     * Bulk actions for houses.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => ['required', 'in:activate,deactivate,delete'],
            'houses' => ['required', 'array'],
            'houses.*' => ['exists:dune_rp_houses,id'],
        ]);

        $houses = House::whereIn('id', $request->houses)->get();
        $count = 0;

        foreach ($houses as $house) {
            switch ($request->action) {
                case 'activate':
                    $house->update(['is_active' => true]);
                    $count++;
                    break;
                
                case 'deactivate':
                    $house->update(['is_active' => false]);
                    $count++;
                    break;
                
                case 'delete':
                    if (!$house->characters()->exists()) {
                        $house->delete();
                        $count++;
                    }
                    break;
            }
        }

        $actionText = [
            'activate' => 'activated',
            'deactivate' => 'deactivated',
            'delete' => 'deleted',
        ];

        return back()->with('success', "{$count} houses {$actionText[$request->action]}");
    }
}
