@extends('layouts.app')

@section('title', trans('dune-rp::messages.houses.title'))

@push('styles')
    <link href="{{ plugin_asset('dune-rp', 'css/dune-rp.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="dune-container">
    {{-- Header Section --}}
    <div class="page-header dune-panel" style="text-align: center; padding: 40px 20px;">
        <h1 class="dune-heading" style="font-size: 2.5rem; margin-bottom: 15px;">
            <i class="bi bi-shield"></i> {{ trans('dune-rp::messages.houses.title') }}
        </h1>
        <p style="font-size: 1.1rem; color: var(--dune-sand); max-width: 600px; margin: 0 auto;">
            {{ trans('dune-rp::messages.houses.description') }}
        </p>
    </div>

    {{-- Filter and Actions Bar --}}
    <div class="filters-section dune-panel" style="padding: 20px; margin-bottom: 30px;">
        <form method="GET" class="filter-form" style="display: flex; gap: 15px; align-items: end; flex-wrap: wrap;">
            <div class="filter-group" style="flex: 1; min-width: 200px;">
                <label for="search" style="display: block; margin-bottom: 5px; color: var(--dune-sand);">{{ trans('dune-rp::messages.common.search') }}</label>
                <input type="text" id="search" name="search" class="dune-input" 
                       value="{{ request('search') }}" 
                       placeholder="Nom de maison, devise...">
            </div>
            
            <div class="filter-group" style="min-width: 150px;">
                <label for="sort" style="display: block; margin-bottom: 5px; color: var(--dune-sand);">{{ trans('dune-rp::messages.common.sort') }}</label>
                <select id="sort" name="sort" class="dune-select">
                    <option value="influence" {{ request('sort') == 'influence' ? 'selected' : '' }}>Par Influence</option>
                    <option value="spice" {{ request('sort') == 'spice' ? 'selected' : '' }}>Par Épice</option>
                    <option value="members" {{ request('sort') == 'members' ? 'selected' : '' }}>Par Membres</option>
                    <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Par Nom</option>
                </select>
            </div>
            
            <div class="filter-actions">
                <button type="submit" class="dune-button" style="margin-right: 10px;">
                    <i class="bi bi-funnel"></i> {{ trans('dune-rp::messages.common.filter') }}
                </button>
                <a href="{{ route('dune-rp.houses.index') }}" class="dune-button secondary">
                    <i class="bi bi-arrow-clockwise"></i> {{ trans('dune-rp::messages.common.reset') }}
                </a>
            </div>
        </form>
    </div>

    {{-- Quick Actions --}}
    <div class="quick-actions" style="display: flex; gap: 15px; justify-content: center; margin-bottom: 30px; flex-wrap: wrap;">
        <a href="{{ route('dune-rp.houses.recruitment') }}" class="dune-button">
            <i class="bi bi-person-plus"></i> {{ trans('dune-rp::messages.houses.recruitment.title') }}
        </a>
        <a href="{{ route('dune-rp.houses.compare') }}" class="dune-button secondary">
            <i class="bi bi-bar-chart"></i> {{ trans('dune-rp::messages.houses.compare') }}
        </a>
        <a href="{{ route('dune-rp.houses.leaderboard') }}" class="dune-button secondary">
            <i class="bi bi-trophy"></i> {{ trans('dune-rp::messages.houses.leaderboard.title') }}
        </a>
    </div>

    {{-- Houses Grid --}}
    @if($houses->count() > 0)
        <div class="houses-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 25px; margin-bottom: 40px;">
            @foreach($houses as $house)
                <div class="house-card dune-panel fade-in" style="padding: 25px; position: relative;">
                    {{-- House Rank Badge --}}
                    <div class="house-rank-badge" style="position: absolute; top: -10px; right: 20px; background: var(--dune-spice); color: white; padding: 5px 15px; border-radius: 15px; font-size: 0.9rem; font-weight: bold;">
                        #{{ $loop->iteration }}
                    </div>
                    
                    {{-- House Header --}}
                    <div class="house-header" style="text-align: center; margin-bottom: 20px;">
                        @if($house->sigil_url)
                            <div class="house-sigil" style="background-image: url('{{ $house->getImageUrl() }}'); margin-bottom: 15px;">
                            </div>
                        @else
                            <div class="house-sigil" style="background: linear-gradient(45deg, var(--dune-sand), var(--dune-spice)); display: flex; align-items: center; justify-content: center; margin-bottom: 15px;">
                                <i class="bi bi-shield" style="font-size: 2rem; color: white;"></i>
                            </div>
                        @endif
                        
                        <h3 style="margin: 0 0 10px 0; color: var(--dune-spice-glow); font-size: 1.4rem;">
                            {{ $house->name }}
                        </h3>
                        
                        @if($house->motto)
                            <p style="margin: 0 0 15px 0; font-style: italic; color: var(--dune-sand); font-size: 0.95rem;">
                                "{{ $house->motto }}"
                            </p>
                        @endif
                        
                        @if($house->leader)
                            <div class="house-leader" style="font-size: 0.9rem; color: var(--dune-blue-eyes);">
                                <i class="bi bi-person-crown"></i> {{ trans('dune-rp::messages.houses.leader') }}: {{ $house->leader->name }}
                            </div>
                        @endif
                    </div>

                    {{-- House Stats --}}
                    <div class="house-stats">
                        <div class="stat-item">
                            <i class="bi bi-people" style="color: var(--dune-blue-eyes); margin-bottom: 5px; font-size: 1.2rem;"></i>
                            <span class="label">{{ trans('dune-rp::messages.houses.members') }}</span>
                            <span class="value">{{ $house->active_characters_count }}</span>
                        </div>
                        
                        <div class="stat-item">
                            <i class="bi bi-gem" style="color: var(--dune-spice-glow); margin-bottom: 5px; font-size: 1.2rem;"></i>
                            <span class="label">{{ trans('dune-rp::messages.houses.influence') }}</span>
                            <span class="value spice-glow">{{ number_format($house->influence_points) }}</span>
                        </div>
                        
                        <div class="stat-item">
                            <i class="bi bi-lightning" style="color: var(--dune-spice); margin-bottom: 5px; font-size: 1.2rem;"></i>
                            <span class="label">{{ trans('dune-rp::messages.houses.spice_reserves') }}</span>
                            <span class="value spice-glow">{{ number_format($house->spice_reserves, 0) }}</span>
                        </div>
                        
                        @if($house->homeworld)
                            <div class="stat-item">
                                <i class="bi bi-globe" style="color: var(--dune-sand); margin-bottom: 5px; font-size: 1.2rem;"></i>
                                <span class="label">{{ trans('dune-rp::messages.houses.homeworld') }}</span>
                                <span class="value">{{ $house->homeworld }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- House Description --}}
                    @if($house->description)
                        <div class="house-description" style="margin: 20px 0; padding: 15px; background: rgba(30,74,140,0.1); border-radius: 5px; border-left: 3px solid var(--dune-blue-eyes);">
                            <p style="margin: 0; font-size: 0.9rem; line-height: 1.4;">
                                {{ Str::limit(strip_tags($house->parseDescription()), 150) }}
                            </p>
                        </div>
                    @endif

                    {{-- Action Buttons --}}
                    <div class="house-actions" style="display: flex; gap: 10px; justify-content: space-between; margin-top: 20px;">
                        <a href="{{ route('dune-rp.houses.show', $house) }}" class="dune-button" style="flex: 1; text-align: center;">
                            <i class="bi bi-eye"></i> {{ trans('dune-rp::messages.houses.view_details') }}
                        </a>
                        
                        @auth
                            @if(!auth()->user()->characters()->where('house_id', $house->id)->exists())
                                <button class="dune-button secondary" style="flex: 1;" 
                                        onclick="joinHouse({{ $house->id }}, '{{ $house->name }}')"
                                        data-house-id="{{ $house->id }}">
                                    <i class="bi bi-plus-circle"></i> {{ trans('dune-rp::messages.houses.join_house') }}
                                </button>
                            @endif
                        @endauth
                    </div>
                    
                    {{-- Influence Level Badge --}}
                    <div class="influence-level" style="position: absolute; bottom: -10px; left: 50%; transform: translateX(-50%); background: var(--dune-blue-eyes); color: white; padding: 5px 12px; border-radius: 12px; font-size: 0.8rem; white-space: nowrap;">
                        {{ $house->getInfluenceLevel() }}
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($houses->hasPages())
            <div class="pagination-wrapper" style="display: flex; justify-content: center; margin-top: 30px;">
                {{ $houses->appends(request()->query())->links() }}
            </div>
        @endif
    @else
        <div class="empty-state dune-panel" style="text-align: center; padding: 60px 20px;">
            <i class="bi bi-shield-x" style="font-size: 4rem; color: var(--dune-sand-dark); margin-bottom: 20px;"></i>
            <h3 style="color: var(--dune-sand); margin-bottom: 15px;">{{ trans('dune-rp::messages.houses.no_houses') }}</h3>
            <p style="color: var(--dune-sand); margin-bottom: 25px;">Aucune maison ne correspond à vos critères de recherche.</p>
            
            <a href="{{ route('dune-rp.houses.index') }}" class="dune-button">
                <i class="bi bi-arrow-clockwise"></i> {{ trans('dune-rp::messages.common.reset') }}
            </a>
        </div>
    @endif
</div>

{{-- Join House Modal --}}
@auth
<div id="joinHouseModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; align-items: center; justify-content: center;">
    <div class="modal-content dune-panel" style="max-width: 500px; width: 90%; padding: 30px;">
        <h3 class="dune-heading" style="margin-bottom: 20px;">Rejoindre une Maison</h3>
        <p id="joinConfirmText" style="margin-bottom: 25px; color: var(--dune-sand);"></p>
        
        <div class="modal-actions" style="display: flex; gap: 15px; justify-content: end;">
            <button onclick="closeJoinModal()" class="dune-button secondary">
                {{ trans('dune-rp::messages.common.cancel') }}
            </button>
            <button id="confirmJoinBtn" class="dune-button" onclick="confirmJoinHouse()">
                <i class="bi bi-check"></i> {{ trans('dune-rp::messages.common.confirm') }}
            </button>
        </div>
    </div>
</div>
@endauth

<style>
.modal {
    display: none;
}
.modal.show {
    display: flex !important;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .houses-grid {
        grid-template-columns: 1fr !important;
    }
    
    .filter-form {
        flex-direction: column !important;
        align-items: stretch !important;
    }
    
    .filter-group {
        min-width: auto !important;
    }
    
    .quick-actions {
        flex-direction: column !important;
        align-items: stretch !important;
    }
    
    .house-actions {
        flex-direction: column !important;
    }
    
    .stat-item {
        padding: 8px !important;
        font-size: 0.9rem !important;
    }
}

/* Animation delays for staggered appearance */
.house-card:nth-child(1) { animation-delay: 0.1s; }
.house-card:nth-child(2) { animation-delay: 0.2s; }
.house-card:nth-child(3) { animation-delay: 0.3s; }
.house-card:nth-child(4) { animation-delay: 0.4s; }
.house-card:nth-child(5) { animation-delay: 0.5s; }
.house-card:nth-child(6) { animation-delay: 0.6s; }
</style>

<script>
@auth
let selectedHouseId = null;

function joinHouse(houseId, houseName) {
    selectedHouseId = houseId;
    document.getElementById('joinConfirmText').textContent = 
        `Êtes-vous sûr de vouloir rejoindre la Maison ${houseName} ? Cette action peut nécessiter l'approbation d'un administrateur.`;
    document.getElementById('joinHouseModal').classList.add('show');
}

function closeJoinModal() {
    document.getElementById('joinHouseModal').classList.remove('show');
    selectedHouseId = null;
}

async function confirmJoinHouse() {
    if (!selectedHouseId) return;
    
    const button = document.getElementById('confirmJoinBtn');
    button.disabled = true;
    button.innerHTML = '<i class="bi bi-hourglass-split"></i> Traitement...';
    
    try {
        const response = await fetch(`/dune-rp/houses/${selectedHouseId}/join`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Show success message
            showAlert('success', data.message || 'Demande envoyée avec succès !');
            
            // Hide the join button for this house
            const houseButton = document.querySelector(`[data-house-id="${selectedHouseId}"]`);
            if (houseButton) {
                houseButton.style.display = 'none';
            }
            
            closeJoinModal();
        } else {
            showAlert('error', data.message || 'Une erreur est survenue.');
        }
    } catch (error) {
        showAlert('error', 'Erreur de connexion. Veuillez réessayer.');
    }
    
    button.disabled = false;
    button.innerHTML = '<i class="bi bi-check"></i> {{ trans("dune-rp::messages.common.confirm") }}';
}

function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `dune-alert ${type}`;
    alertDiv.textContent = message;
    alertDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 2000;
        max-width: 400px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

// Close modal when clicking outside
document.getElementById('joinHouseModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeJoinModal();
    }
});
@endauth
</script>
@endsection

@push('scripts')
    <script src="{{ plugin_asset('dune-rp', 'js/dune-rp.js') }}"></script>
@endpush
