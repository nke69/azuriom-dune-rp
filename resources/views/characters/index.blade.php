@extends('layouts.app')

@section('title', trans('dune-rp::messages.characters.title'))

@push('styles')
    <link href="{{ plugin_asset('dune-rp', 'css/dune-rp.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="dune-container">
    {{-- Header Section --}}
    <div class="page-header dune-panel" style="text-align: center; padding: 40px 20px;">
        <h1 class="dune-heading" style="font-size: 2.5rem; margin-bottom: 15px;">
            <i class="bi bi-people"></i> {{ trans('dune-rp::messages.characters.title') }}
        </h1>
        <p style="font-size: 1.1rem; color: var(--dune-sand); max-width: 600px; margin: 0 auto;">
            Découvrez les héros et héroïnes qui façonnent l'univers de Dune
        </p>
    </div>

    {{-- Filter Section --}}
    <div class="filters-section dune-panel" style="padding: 25px; margin-bottom: 30px;">
        <form method="GET" class="filter-form" style="display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 20px; align-items: end;">
            <div class="filter-group">
                <label for="search" style="display: block; margin-bottom: 8px; color: var(--dune-sand); font-weight: bold;">
                    <i class="bi bi-search"></i> {{ trans('dune-rp::messages.common.search') }}
                </label>
                <input type="text" id="search" name="search" class="dune-input" 
                       value="{{ request('search') }}" 
                       placeholder="Nom du personnage ou joueur...">
            </div>
            
            <div class="filter-group">
                <label for="house_id" style="display: block; margin-bottom: 8px; color: var(--dune-sand); font-weight: bold;">
                    <i class="bi bi-shield"></i> {{ trans('dune-rp::messages.characters.house') }}
                </label>
                <select id="house_id" name="house_id" class="dune-select">
                    <option value="">{{ trans('dune-rp::messages.common.all') }}</option>
                    @foreach($houses as $house)
                        <option value="{{ $house->id }}" {{ request('house_id') == $house->id ? 'selected' : '' }}>
                            {{ $house->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="filter-group">
                <label for="status" style="display: block; margin-bottom: 8px; color: var(--dune-sand); font-weight: bold;">
                    <i class="bi bi-activity"></i> {{ trans('dune-rp::messages.common.status') }}
                </label>
                <select id="status" name="status" class="dune-select">
                    <option value="">{{ trans('dune-rp::messages.common.all') }}</option>
                    @foreach(\Azuriom\Plugin\DuneRp\Models\Character::STATUSES as $statusKey => $statusName)
                        <option value="{{ $statusKey }}" {{ request('status') == $statusKey ? 'selected' : '' }}>
                            {{ $statusName }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="filter-actions" style="display: flex; gap: 10px;">
                <button type="submit" class="dune-button">
                    <i class="bi bi-funnel"></i> {{ trans('dune-rp::messages.common.filter') }}
                </button>
                <a href="{{ route('dune-rp.characters.index') }}" class="dune-button secondary">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- Quick Actions --}}
    <div class="quick-actions" style="display: flex; gap: 15px; justify-content: center; margin-bottom: 30px; flex-wrap: wrap;">
        @auth
            @if(!auth()->user()->characters()->exists())
                <a href="{{ route('dune-rp.characters.create') }}" class="dune-button">
                    <i class="bi bi-person-plus"></i> {{ trans('dune-rp::messages.characters.create') }}
                </a>
            @else
                <a href="{{ route('dune-rp.characters.my') }}" class="dune-button">
                    <i class="bi bi-person-badge"></i> {{ trans('dune-rp::messages.characters.my_character') }}
                </a>
            @endif
        @endauth
        
        <a href="{{ route('dune-rp.characters.gallery') }}" class="dune-button secondary">
            <i class="bi bi-images"></i> {{ trans('dune-rp::messages.characters.gallery') }}
        </a>
        
        <div class="view-toggle" style="display: flex; gap: 5px; border: 2px solid var(--dune-sand); border-radius: 25px; padding: 4px;">
            <button onclick="switchView('grid')" id="gridViewBtn" class="view-btn active" style="padding: 8px 12px; border: none; background: var(--dune-spice); color: white; border-radius: 20px; cursor: pointer; font-family: var(--font-dune);">
                <i class="bi bi-grid-3x3-gap"></i> Grille
            </button>
            <button onclick="switchView('list')" id="listViewBtn" class="view-btn" style="padding: 8px 12px; border: none; background: transparent; color: var(--dune-sand); border-radius: 20px; cursor: pointer; font-family: var(--font-dune);">
                <i class="bi bi-list"></i> Liste
            </button>
        </div>
    </div>

    {{-- Characters Display --}}
    @if($characters->count() > 0)
        {{-- Grid View --}}
        <div id="charactersGrid" class="characters-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 25px; margin-bottom: 40px;">
            @foreach($characters as $character)
                <div class="character-card dune-panel fade-in" style="padding: 25px; text-align: center; position: relative; overflow: hidden;">
                    {{-- Status Badge --}}
                    <div class="status-badge status-{{ $character->status }}" style="position: absolute; top: 15px; right: 15px; padding: 4px 8px; border-radius: 12px; font-size: 0.7rem; font-weight: bold; text-transform: uppercase;">
                        {{ $character->getStatusName() }}
                    </div>
                    
                    {{-- Character Avatar --}}
                    <div class="character-avatar" style="margin-bottom: 20px; position: relative;">
                        @if($character->avatar_url)
                            <img src="{{ $character->getImageUrl() }}" alt="{{ $character->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            <div style="width: 100%; height: 100%; background: linear-gradient(45deg, var(--dune-sand), var(--dune-spice)); display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                <i class="bi bi-person" style="font-size: 2.5rem; color: white;"></i>
                            </div>
                        @endif
                        
                        {{-- Blue Eyes Effect if Fremen --}}
                        @if($character->hasAbility('fremen_skills'))
                            <div class="blue-eyes-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: radial-gradient(circle at 35% 35%, rgba(30,74,140,0.6) 2px, transparent 3px), radial-gradient(circle at 65% 35%, rgba(30,74,140,0.6) 2px, transparent 3px); border-radius: 50%; pointer-events: none;"></div>
                        @endif
                    </div>
                    
                    {{-- Character Info --}}
                    <h3 style="margin: 0 0 10px 0; color: var(--dune-spice-glow); font-size: 1.3rem;">
                        {{ $character->name }}
                    </h3>
                    
                    @if($character->title)
                        <p style="margin: 0 0 10px 0; font-style: italic; color: var(--dune-sand); font-size: 0.95rem;">
                            {{ $character->title }}
                        </p>
                    @endif
                    
                    {{-- House Info --}}
                    @if($character->house)
                        <div class="character-house" style="margin-bottom: 15px; display: flex; align-items: center; justify-content: center; gap: 8px;">
                            <div class="house-color" style="width: 12px; height: 12px; border-radius: 50%; background: {{ $character->house->color ?? 'var(--dune-sand)' }};"></div>
                            <span style="color: {{ $character->house->color ?? 'var(--dune-sand)' }}; font-weight: bold;">
                                {{ $character->house->name }}
                            </span>
                        </div>
                    @else
                        <div class="character-house" style="margin-bottom: 15px;">
                            <span style="color: var(--dune-sand-dark); font-style: italic;">
                                {{ trans('dune-rp::messages.characters.no_house') }}
                            </span>
                        </div>
                    @endif
                    
                    {{-- Character Details --}}
                    <div class="character-details" style="margin-bottom: 20px; font-size: 0.9rem; color: var(--dune-sand);">
                        @if($character->age)
                            <div style="margin-bottom: 5px;">
                                <i class="bi bi-calendar"></i> {{ $character->getAgeDisplay() }}
                            </div>
                        @endif
                        
                        @if($character->birthworld)
                            <div style="margin-bottom: 5px;">
                                <i class="bi bi-globe"></i> {{ $character->birthworld }}
                            </div>
                        @endif
                        
                        <div>
                            <i class="bi bi-person"></i> {{ $character->user->name }}
                        </div>
                    </div>
                    
                    {{-- Character Abilities --}}
                    @if($character->special_abilities && count($character->special_abilities) > 0)
                        <div class="character-abilities" style="margin-bottom: 20px;">
                            @foreach($character->getAbilitiesNames() as $ability)
                                <span class="ability-tag">{{ $ability }}</span>
                            @endforeach
                        </div>
                    @endif
                    
                    {{-- Spice Addiction Level --}}
                    @if($character->spice_addiction_level > 0)
                        <div class="addiction-level" style="margin-bottom: 15px;">
                            <div style="font-size: 0.8rem; color: var(--dune-sand); margin-bottom: 5px;">
                                Addiction à l'Épice
                            </div>
                            <div class="addiction-bar" style="background: rgba(44, 24, 16, 0.8); height: 8px; border-radius: 4px; overflow: hidden;">
                                <div style="height: 100%; background: linear-gradient(90deg, var(--dune-spice), var(--dune-spice-glow)); width: {{ ($character->spice_addiction_level / 4) * 100 }}%; transition: width 0.3s;"></div>
                            </div>
                        </div>
                    @endif
                    
                    {{-- Action Button --}}
                    <a href="{{ route('dune-rp.characters.show', $character) }}" class="dune-button" style="width: 100%;">
                        <i class="bi bi-eye"></i> {{ trans('dune-rp::messages.common.view') }}
                    </a>
                    
                    {{-- Creation Date --}}
                    <div style="margin-top: 15px; font-size: 0.8rem; color: var(--dune-sand-dark);">
                        <i class="bi bi-clock"></i> {{ $character->created_at->diffForHumans() }}
                    </div>
                </div>
            @endforeach
        </div>

        {{-- List View --}}
        <div id="charactersList" class="characters-list" style="display: none;">
            <div class="dune-panel" style="padding: 0; overflow: hidden;">
                <table class="dune-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Avatar</th>
                            <th>{{ trans('dune-rp::messages.characters.form.name') }}</th>
                            <th>{{ trans('dune-rp::messages.characters.house') }}</th>
                            <th>{{ trans('dune-rp::messages.common.status') }}</th>
                            <th>{{ trans('dune-rp::messages.characters.age') }}</th>
                            <th>Joueur</th>
                            <th style="width: 120px;">{{ trans('dune-rp::messages.common.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($characters as $character)
                            <tr>
                                <td style="padding: 8px;">
                                    <div style="width: 50px; height: 50px; border-radius: 50%; overflow: hidden; border: 2px solid var(--dune-sand);">
                                        @if($character->avatar_url)
                                            <img src="{{ $character->getImageUrl() }}" alt="{{ $character->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                        @else
                                            <div style="width: 100%; height: 100%; background: var(--dune-sand); display: flex; align-items: center; justify-content: center;">
                                                <i class="bi bi-person" style="color: white; font-size: 1.2rem;"></i>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <strong style="color: var(--dune-spice-glow);">{{ $character->name }}</strong>
                                        @if($character->title)
                                            <div style="font-size: 0.9rem; color: var(--dune-sand); font-style: italic;">{{ $character->title }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($character->house)
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <div style="width: 10px; height: 10px; border-radius: 50%; background: {{ $character->house->color ?? 'var(--dune-sand)' }};"></div>
                                            <span style="color: {{ $character->house->color ?? 'var(--dune-sand)' }};">{{ $character->house->name }}</span>
                                        </div>
                                    @else
                                        <span style="color: var(--dune-sand-dark); font-style: italic;">Sans Maison</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="status-{{ $character->status }}" style="font-weight: bold;">
                                        {{ $character->getStatusName() }}
                                    </span>
                                </td>
                                <td>
                                    {{ $character->age ? $character->age . ' ans' : '-' }}
                                </td>
                                <td style="color: var(--dune-sand);">
                                    {{ $character->user->name }}
                                </td>
                                <td>
                                    <a href="{{ route('dune-rp.characters.show', $character) }}" class="dune-button secondary" style="font-size: 0.9rem; padding: 6px 12px;">
                                        <i class="bi bi-eye"></i> Voir
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if($characters->hasPages())
            <div class="pagination-wrapper" style="display: flex; justify-content: center; margin-top: 30px;">
                {{ $characters->appends(request()->query())->links() }}
            </div>
        @endif
    @else
        <div class="empty-state dune-panel" style="text-align: center; padding: 60px 20px;">
            <i class="bi bi-person-x" style="font-size: 4rem; color: var(--dune-sand-dark); margin-bottom: 20px;"></i>
            <h3 style="color: var(--dune-sand); margin-bottom: 15px;">{{ trans('dune-rp::messages.characters.no_characters') }}</h3>
            <p style="color: var(--dune-sand); margin-bottom: 25px;">Aucun personnage ne correspond à vos critères de recherche.</p>
            
            <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                <a href="{{ route('dune-rp.characters.index') }}" class="dune-button secondary">
                    <i class="bi bi-arrow-clockwise"></i> {{ trans('dune-rp::messages.common.reset') }}
                </a>
                @auth
                    @if(!auth()->user()->characters()->exists())
                        <a href="{{ route('dune-rp.characters.create') }}" class="dune-button">
                            <i class="bi bi-person-plus"></i> {{ trans('dune-rp::messages.characters.create') }}
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    @endif
</div>

<style>
/* Status colors */
.status-alive { background: #4caf50; color: white; }
.status-missing { background: #ff9800; color: white; }
.status-deceased { background: #f44336; color: white; }
.status-exiled { background: #9c27b0; color: white; }

/* Table status colors */
.status-alive { color: #4caf50; }
.status-missing { color: #ff9800; }
.status-deceased { color: #f44336; }
.status-exiled { color: #9c27b0; }

/* View toggle active state */
.view-btn.active {
    background: var(--dune-spice) !important;
    color: white !important;
}

/* Animation delays for staggered appearance */
.character-card:nth-child(1) { animation-delay: 0.1s; }
.character-card:nth-child(2) { animation-delay: 0.2s; }
.character-card:nth-child(3) { animation-delay: 0.3s; }
.character-card:nth-child(4) { animation-delay: 0.4s; }
.character-card:nth-child(5) { animation-delay: 0.5s; }
.character-card:nth-child(6) { animation-delay: 0.6s; }

/* Responsive */
@media (max-width: 768px) {
    .filter-form {
        grid-template-columns: 1fr !important;
        gap: 15px !important;
    }
    
    .characters-grid {
        grid-template-columns: 1fr !important;
    }
    
    .quick-actions {
        flex-direction: column !important;
        align-items: stretch !important;
    }
    
    .view-toggle {
        align-self: center !important;
    }
    
    .dune-table {
        font-size: 0.9rem;
    }
    
    .dune-table th,
    .dune-table td {
        padding: 8px !important;
    }
}

/* Hover effects */
.character-card {
    transition: all 0.3s ease;
}

.character-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(214, 166, 95, 0.3);
}

.character-card:hover .character-avatar img {
    transform: scale(1.1);
}

.character-avatar img {
    transition: transform 0.3s ease;
}
</style>

<script>
function switchView(viewType) {
    const gridView = document.getElementById('charactersGrid');
    const listView = document.getElementById('charactersList');
    const gridBtn = document.getElementById('gridViewBtn');
    const listBtn = document.getElementById('listViewBtn');
    
    if (viewType === 'grid') {
        gridView.style.display = 'grid';
        listView.style.display = 'none';
        gridBtn.classList.add('active');
        listBtn.classList.remove('active');
    } else {
        gridView.style.display = 'none';
        listView.style.display = 'block';
        listBtn.classList.add('active');
        gridBtn.classList.remove('active');
    }
    
    // Save preference
    localStorage.setItem('dune-rp-characters-view', viewType);
}

// Load saved view preference
document.addEventListener('DOMContentLoaded', function() {
    const savedView = localStorage.getItem('dune-rp-characters-view');
    if (savedView && savedView === 'list') {
        switchView('list');
    }
});

// Auto-submit form on filter change
document.addEventListener('DOMContentLoaded', function() {
    const selects = document.querySelectorAll('#house_id, #status');
    selects.forEach(select => {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });
});
</script>
@endsection

@push('scripts')
    <script src="{{ plugin_asset('dune-rp', 'js/dune-rp.js') }}"></script>
@endpush
