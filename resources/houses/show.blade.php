@extends('layouts.app')

@section('title', $house->name . ' - ' . trans('dune-rp::messages.houses.title'))

@push('styles')
    <link href="{{ plugin_asset('dune-rp', 'css/dune-rp.css') }}" rel="stylesheet">
    <style>
        .house-theme {
            --house-color: {{ $house->color ?? '#D6A65F' }};
        }
        .house-accent {
            color: var(--house-color) !important;
        }
        .house-border {
            border-color: var(--house-color) !important;
        }
        .house-bg {
            background: linear-gradient(135deg, var(--house-color)20, transparent) !important;
        }
    </style>
@endpush

@section('content')
<div class="dune-container house-theme">
    {{-- House Header --}}
    <div class="house-header dune-panel" style="text-align: center; padding: 50px 20px; background: linear-gradient(135deg, rgba(15,15,35,0.9), {{ $house->color }}40);">
        <div class="house-sigil-large" style="width: 120px; height: 120px; margin: 0 auto 25px; border: 4px solid var(--house-color); border-radius: 50%; position: relative;">
            @if($house->sigil_url)
                <img src="{{ $house->getImageUrl() }}" alt="{{ $house->name }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
            @else
                <div style="width: 100%; height: 100%; background: linear-gradient(45deg, var(--house-color), var(--dune-spice)); display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                    <i class="bi bi-shield" style="font-size: 3rem; color: white;"></i>
                </div>
            @endif
            
            {{-- House influence level badge --}}
            <div class="influence-badge" style="position: absolute; bottom: -15px; left: 50%; transform: translateX(-50%); background: var(--house-color); color: white; padding: 8px 16px; border-radius: 20px; font-size: 0.9rem; font-weight: bold; white-space: nowrap; border: 2px solid var(--dune-space);">
                {{ $house->getInfluenceLevel() }}
            </div>
        </div>
        
        <h1 class="dune-heading house-accent" style="font-size: 2.8rem; margin-bottom: 15px;">
            {{ $house->name }}
        </h1>
        
        @if($house->motto)
            <p style="font-size: 1.3rem; font-style: italic; color: var(--dune-sand); margin-bottom: 20px; max-width: 600px; margin-left: auto; margin-right: auto;">
                "{{ $house->motto }}"
            </p>
        @endif
        
        @if($house->leader)
            <div class="house-leader-info" style="background: rgba(0,0,0,0.3); display: inline-block; padding: 12px 24px; border-radius: 25px; border: 2px solid var(--house-color);">
                <i class="bi bi-person-crown house-accent"></i>
                <strong>{{ trans('dune-rp::messages.houses.leader') }}:</strong> {{ $house->leader->name }}
            </div>
        @endif
    </div>

    <div class="house-content" style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; margin-top: 30px;">
        {{-- Main Content --}}
        <div class="house-main-content">
            {{-- House Description --}}
            @if($house->description)
                <div class="house-description dune-panel" style="padding: 30px; margin-bottom: 30px;">
                    <h2 class="dune-heading house-accent" style="margin-bottom: 20px;">
                        <i class="bi bi-book"></i> {{ trans('dune-rp::messages.houses.about') }}
                    </h2>
                    <div class="description-content" style="line-height: 1.6; font-size: 1.1rem;">
                        {!! $house->parseDescription() !!}
                    </div>
                </div>
            @endif

            {{-- House Members --}}
            <div class="house-members dune-panel" style="padding: 30px; margin-bottom: 30px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                    <h2 class="dune-heading house-accent" style="margin: 0;">
                        <i class="bi bi-people"></i> {{ trans('dune-rp::messages.houses.members') }}
                        <span class="members-count spice-glow">({{ $house->characters->count() }})</span>
                    </h2>
                    
                    <a href="{{ route('dune-rp.characters.by-house', $house) }}" class="dune-button secondary">
                        {{ trans('dune-rp::messages.characters.view_all') }}
                    </a>
                </div>
                
                @if($house->characters->count() > 0)
                    <div class="members-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
                        @foreach($house->characters->take(6) as $character)
                            <div class="member-card" style="background: rgba(30,74,140,0.1); padding: 20px; border-radius: 10px; border: 1px solid var(--house-color); display: flex; align-items: center; gap: 15px;">
                                <div class="character-avatar" style="width: 60px; height: 60px;">
                                    @if($character->avatar_url)
                                        <img src="{{ $character->getImageUrl() }}" alt="{{ $character->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                    @else
                                        <div style="width: 100%; height: 100%; background: var(--house-color); display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                            <i class="bi bi-person" style="font-size: 1.5rem; color: white;"></i>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="character-info" style="flex: 1;">
                                    <h4 style="margin: 0 0 5px 0; color: var(--dune-spice-glow);">
                                        {{ $character->name }}
                                    </h4>
                                    @if($character->title)
                                        <p style="margin: 0 0 5px 0; font-style: italic; color: var(--house-color);">
                                            {{ $character->title }}
                                        </p>
                                    @endif
                                    
                                    <div class="character-meta" style="display: flex; gap: 15px; font-size: 0.9rem; color: var(--dune-sand);">
                                        <span>
                                            <i class="bi bi-clock"></i> {{ $character->created_at->diffForHumans() }}
                                        </span>
                                        <span class="status-{{ $character->status }}">
                                            <i class="bi bi-circle-fill" style="font-size: 0.6rem;"></i> {{ $character->getStatusName() }}
                                        </span>
                                    </div>
                                </div>
                                
                                <a href="{{ route('dune-rp.characters.show', $character) }}" class="dune-button secondary" style="padding: 8px 12px; font-size: 0.9rem;">
                                    {{ trans('dune-rp::messages.common.view') }}
                                </a>
                            </div>
                        @endforeach
                    </div>
                    
                    @if($house->characters->count() > 6)
                        <div style="text-align: center; margin-top: 20px;">
                            <a href="{{ route('dune-rp.characters.by-house', $house) }}" class="dune-button">
                                {{ trans('dune-rp::messages.characters.view_all') }} ({{ $house->characters->count() - 6 }} {{ trans('dune-rp::messages.common.more') }})
                            </a>
                        </div>
                    @endif
                @else
                    <div class="empty-members" style="text-align: center; padding: 40px;">
                        <i class="bi bi-people" style="font-size: 3rem; color: var(--dune-sand-dark); margin-bottom: 15px;"></i>
                        <p style="color: var(--dune-sand);">{{ trans('dune-rp::messages.houses.no_members') }}</p>
                    </div>
                @endif
            </div>

            {{-- Recent Events --}}
            @if($house->events->count() > 0)
                <div class="house-events dune-panel" style="padding: 30px;">
                    <h2 class="dune-heading house-accent" style="margin-bottom: 25px;">
                        <i class="bi bi-calendar-event"></i> {{ trans('dune-rp::messages.houses.recent_events') }}
                    </h2>
                    
                    <div class="events-list" style="display: flex; flex-direction: column; gap: 15px;">
                        @foreach($house->events->take(5) as $event)
                            <div class="event-item" style="display: flex; align-items: center; padding: 15px; background: rgba(30,74,140,0.1); border-radius: 8px; border-left: 4px solid var(--house-color);">
                                <div class="event-info" style="flex: 1;">
                                    <h5 style="margin: 0 0 5px 0; color: var(--dune-spice-glow);">{{ $event->title }}</h5>
                                    
                                    <div class="event-meta" style="display: flex; gap: 20px; font-size: 0.9rem; color: var(--dune-sand);">
                                        <span>
                                            <i class="bi bi-clock"></i> {{ $event->getFormattedDate() }}
                                        </span>
                                        @if($event->location)
                                            <span>
                                                <i class="bi bi-geo-alt"></i> {{ $event->location }}
                                            </span>
                                        @endif
                                        <span class="event-status-{{ $event->status }}">
                                            {{ $event->getStatusName() }}
                                        </span>
                                    </div>
                                </div>
                                
                                <a href="{{ route('dune-rp.events.show', $event) }}" class="dune-button secondary" style="padding: 6px 12px; font-size: 0.9rem;">
                                    {{ trans('dune-rp::messages.common.details') }}
                                </a>
                            </div>
                        @endforeach
                    </div>
                    
                    <div style="text-align: center; margin-top: 20px;">
                        <a href="{{ route('dune-rp.events.index', ['house_id' => $house->id]) }}" class="dune-button">
                            {{ trans('dune-rp::messages.events.view_all') }}
                        </a>
                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="house-sidebar">
            {{-- House Stats --}}
            <div class="house-stats-card dune-panel" style="padding: 25px; margin-bottom: 25px; background: linear-gradient(135deg, rgba(30,74,140,0.1), rgba(0,0,0,0.2));">
                <h3 class="dune-heading house-accent" style="text-align: center; margin-bottom: 25px; font-size: 1.4rem;">
                    {{ trans('dune-rp::messages.statistics.title') }}
                </h3>
                
                <div class="stats-grid" style="display: grid; grid-template-columns: 1fr; gap: 15px;">
                    <div class="stat-item house-border" style="text-align: center; padding: 15px; border-radius: 8px;">
                        <div class="stat-value spice-glow" style="font-size: 1.8rem; font-weight: bold; margin-bottom: 5px;">
                            {{ number_format($house->influence_points) }}
                        </div>
                        <div class="stat-label">{{ trans('dune-rp::messages.houses.influence') }}</div>
                    </div>
                    
                    <div class="stat-item house-border" style="text-align: center; padding: 15px; border-radius: 8px;">
                        <div class="stat-value spice-glow" style="font-size: 1.8rem; font-weight: bold; margin-bottom: 5px;">
                            {{ number_format($house->spice_reserves, 0) }}
                        </div>
                        <div class="stat-label">{{ trans('dune-rp::messages.houses.spice_reserves') }}</div>
                    </div>
                    
                    <div class="stat-item house-border" style="text-align: center; padding: 15px; border-radius: 8px;">
                        <div class="stat-value spice-glow" style="font-size: 1.8rem; font-weight: bold; margin-bottom: 5px;">
                            {{ $houseStats['active_members'] }}
                        </div>
                        <div class="stat-label">{{ trans('dune-rp::messages.houses.active_members') }}</div>
                    </div>
                    
                    <div class="stat-item house-border" style="text-align: center; padding: 15px; border-radius: 8px;">
                        <div class="stat-value spice-glow" style="font-size: 1.8rem; font-weight: bold; margin-bottom: 5px;">
                            {{ $houseStats['completed_events'] }}
                        </div>
                        <div class="stat-label">{{ trans('dune-rp::messages.events.completed') }}</div>
                    </div>
                    
                    <div class="stat-item house-border" style="text-align: center; padding: 15px; border-radius: 8px;">
                        <div class="stat-value spice-glow" style="font-size: 1.4rem; font-weight: bold; margin-bottom: 5px;">
                            {{ number_format($houseStats['spice_earned_this_month'], 0) }}
                        </div>
                        <div class="stat-label">{{ trans('dune-rp::messages.houses.spice_earned') }}</div>
                    </div>
                </div>
            </div>

            {{-- House Info --}}
            <div class="house-info-card dune-panel" style="padding: 25px; margin-bottom: 25px;">
                <h3 class="dune-heading house-accent" style="margin-bottom: 20px; font-size: 1.4rem;">
                    {{ trans('dune-rp::messages.common.details') }}
                </h3>
                
                <div class="info-list" style="display: flex; flex-direction: column; gap: 12px;">
                    @if($house->homeworld)
                        <div class="info-item" style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid rgba(214,166,95,0.2);">
                            <span style="color: var(--dune-sand);"><i class="bi bi-globe"></i> {{ trans('dune-rp::messages.houses.homeworld') }}:</span>
                            <span class="house-accent" style="font-weight: bold;">{{ $house->homeworld }}</span>
                        </div>
                    @endif
                    
                    <div class="info-item" style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid rgba(214,166,95,0.2);">
                        <span style="color: var(--dune-sand);"><i class="bi bi-calendar"></i> {{ trans('dune-rp::messages.common.created') }}:</span>
                        <span class="house-accent">{{ $house->created_at->format('d/m/Y') }}</span>
                    </div>
                    
                    <div class="info-item" style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid rgba(214,166,95,0.2);">
                        <span style="color: var(--dune-sand);"><i class="bi bi-activity"></i> {{ trans('dune-rp::messages.common.status') }}:</span>
                        <span style="color: {{ $house->is_active ? '#4caf50' : '#f44336' }}; font-weight: bold;">
                            {{ $house->is_active ? trans('dune-rp::messages.common.active') : trans('dune-rp::messages.common.inactive') }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="house-actions" style="display: flex; flex-direction: column; gap: 15px;">
                @auth
                    @if(!auth()->user()->characters()->where('house_id', $house->id)->exists())
                        <button class="dune-button" style="width: 100%;" onclick="requestJoinHouse({{ $house->id }})">
                            <i class="bi bi-plus-circle"></i> {{ trans('dune-rp::messages.houses.join_house') }}
                        </button>
                    @else
                        <div class="member-badge" style="text-align: center; padding: 12px; background: var(--house-color); color: white; border-radius: 8px; font-weight: bold;">
                            <i class="bi bi-check-circle"></i> {{ trans('dune-rp::messages.houses.member') }}
                        </div>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="dune-button" style="width: 100%; text-align: center;">
                        <i class="bi bi-box-arrow-in-right"></i> {{ trans('dune-rp::messages.auth.login_to_join') }}
                    </a>
                @endauth
                
                <a href="{{ route('dune-rp.houses.compare') }}" class="dune-button secondary" style="width: 100%; text-align: center;">
                    <i class="bi bi-bar-chart"></i> {{ trans('dune-rp::messages.houses.compare') }}
                </a>
                
                <a href="{{ route('dune-rp.houses.index') }}" class="dune-button secondary" style="width: 100%; text-align: center;">
                    <i class="bi bi-arrow-left"></i> {{ trans('dune-rp::messages.common.back') }}
                </a>
            </div>

            {{-- Recent Transactions --}}
            @if($recentTransactions->count() > 0)
                <div class="recent-transactions dune-panel" style="padding: 20px; margin-top: 25px;">
                    <h4 class="dune-heading house-accent" style="margin-bottom: 15px; font-size: 1.2rem;">
                        {{ trans('dune-rp::messages.houses.recent_transactions') }}
                    </h4>
                    
                    <div class="transactions-list" style="display: flex; flex-direction: column; gap: 8px; max-height: 300px; overflow-y: auto;">
                        @foreach($recentTransactions->take(10) as $transaction)
                            <div class="transaction-item" style="display: flex; justify-content: space-between; align-items: center; padding: 8px; background: rgba(30,74,140,0.1); border-radius: 5px; font-size: 0.9rem;">
                                <div>
                                    <i class="bi {{ $transaction->getTypeIcon() }}" style="color: var(--house-color);"></i>
                                    <span style="color: var(--dune-sand);">{{ $transaction->getTypeName() }}</span>
                                </div>
                                <span class="spice-glow" style="font-weight: bold;">
                                    {{ $transaction->getFormattedAmount() }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                    
                    @if($recentTransactions->count() > 10)
                        <div style="text-align: center; margin-top: 10px;">
                            <a href="{{ route('dune-rp.economy.house-transactions', $house) }}" class="dune-button secondary" style="font-size: 0.9rem; padding: 6px 12px;">
                                {{ trans('dune-rp::messages.common.view_all') }}
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<style>
/* Status colors */
.status-alive { color: #4caf50; }
.status-missing { color: #ff9800; }
.status-deceased { color: #f44336; }
.status-exiled { color: #9c27b0; }

.event-status-planned { color: #2196f3; }
.event-status-ongoing { color: #ff9800; }
.event-status-completed { color: #4caf50; }
.event-status-cancelled { color: #f44336; }

/* Responsive */
@media (max-width: 768px) {
    .house-content {
        grid-template-columns: 1fr !important;
        gap: 20px !important;
    }
    
    .house-header {
        padding: 30px 15px !important;
    }
    
    .house-header h1 {
        font-size: 2rem !important;
    }
    
    .members-grid {
        grid-template-columns: 1fr !important;
    }
    
    .member-card {
        flex-direction: column !important;
        text-align: center !important;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr) !important;
    }
}

/* Scrollbar styling for transaction list */
.transactions-list::-webkit-scrollbar {
    width: 6px;
}

.transactions-list::-webkit-scrollbar-track {
    background: rgba(214,166,95,0.1);
    border-radius: 3px;
}

.transactions-list::-webkit-scrollbar-thumb {
    background: var(--house-color);
    border-radius: 3px;
}
</style>

<script>
@auth
async function requestJoinHouse(houseId) {
    const button = event.target;
    button.disabled = true;
    button.innerHTML = '<i class="bi bi-hourglass-split"></i> {{ trans("dune-rp::messages.common.processing") }}...';
    
    try {
        const response = await fetch(`/dune-rp/houses/${houseId}/join`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification('success', data.message || '{{ trans("dune-rp::messages.houses.join_request_sent") }}');
            
            // Replace button with pending message
            button.outerHTML = '<div class="dune-alert info" style="text-align: center; margin: 0;"><i class="bi bi-clock"></i> {{ trans("dune-rp::messages.houses.join_request_pending") }}</div>';
        } else {
            showNotification('error', data.message || '{{ trans("dune-rp::messages.common.error") }}');
            button.disabled = false;
            button.innerHTML = '<i class="bi bi-plus-circle"></i> {{ trans("dune-rp::messages.houses.join_house") }}';
        }
    } catch (error) {
        showNotification('error', '{{ trans("dune-rp::messages.common.network_error") }}');
        button.disabled = false;
        button.innerHTML = '<i class="bi bi-plus-circle"></i> {{ trans("dune-rp::messages.houses.join_house") }}';
    }
}

function showNotification(type, message) {
    const notification = document.createElement('div');
    notification.className = `dune-alert ${type}`;
    notification.innerHTML = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 2000;
        max-width: 400px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        animation: slideIn 0.3s ease-out;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-in forwards';
        setTimeout(() => notification.remove(), 300);
    }, 4000);
}

// Add slide animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);
@endauth
</script>
@endsection

@push('scripts')
    <script src="{{ plugin_asset('dune-rp', 'js/dune-rp.js') }}"></script>
@endpush
