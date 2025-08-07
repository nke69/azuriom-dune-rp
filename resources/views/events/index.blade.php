@extends('layouts.app')

@section('title', trans('dune-rp::messages.events.title'))

@push('styles')
    <link href="{{ plugin_asset('dune-rp', 'css/dune-rp.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="dune-container">
    {{-- Header Section --}}
    <div class="events-header dune-panel" style="text-align: center; padding: 50px 20px; background: linear-gradient(135deg, rgba(139,0,0,0.3), rgba(30,74,140,0.3));">
        <h1 class="dune-heading" style="font-size: 2.8rem; margin-bottom: 20px;">
            <i class="bi bi-calendar-event"></i> {{ trans('dune-rp::messages.events.title') }}
        </h1>
        <p style="font-size: 1.2rem; color: var(--dune-sand); max-width: 800px; margin: 0 auto;">
            {{ trans('dune-rp::messages.events.description') }}
        </p>
        
        {{-- Quick Stats --}}
        <div class="events-stats" style="display: flex; justify-content: center; gap: 30px; margin-top: 30px; flex-wrap: wrap;">
            <div style="text-align: center; padding: 15px; background: rgba(0,0,0,0.3); border-radius: 15px; border: 2px solid #2196f3; min-width: 120px;">
                <div style="font-size: 1.8rem; font-weight: bold; margin-bottom: 5px; color: #2196f3;">
                    {{ $events->where('status', 'planned')->where('event_date', '>', now())->count() }}
                </div>
                <div style="font-size: 0.9rem; color: var(--dune-sand);">À Venir</div>
            </div>
            
            <div style="text-align: center; padding: 15px; background: rgba(0,0,0,0.3); border-radius: 15px; border: 2px solid #ff9800; min-width: 120px;">
                <div style="font-size: 1.8rem; font-weight: bold; margin-bottom: 5px; color: #ff9800;">
                    {{ $events->where('status', 'ongoing')->count() }}
                </div>
                <div style="font-size: 0.9rem; color: var(--dune-sand);">En Cours</div>
            </div>
            
            <div style="text-align: center; padding: 15px; background: rgba(0,0,0,0.3); border-radius: 15px; border: 2px solid #4caf50; min-width: 120px;">
                <div style="font-size: 1.8rem; font-weight: bold; margin-bottom: 5px; color: #4caf50;">
                    {{ $events->where('status', 'completed')->count() }}
                </div>
                <div style="font-size: 0.9rem; color: var(--dune-sand);">Terminés</div>
            </div>
        </div>
    </div>

    {{-- Filters and Actions --}}
    <div class="events-controls" style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; margin: 30px 0;">
        {{-- Filters Section --}}
        <div class="filters-section dune-panel" style="padding: 25px;">
            <h2 style="margin: 0 0 20px 0; color: var(--dune-spice-glow); font-size: 1.4rem;">
                <i class="bi bi-funnel"></i> Filtres & Recherche
            </h2>
            
            <form method="GET" class="filter-form" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 20px; align-items: end;">
                {{-- Status Filter --}}
                <div class="filter-group">
                    <label for="status" style="display: block; margin-bottom: 8px; color: var(--dune-sand); font-weight: bold;">
                        <i class="bi bi-activity"></i> Statut
                    </label>
                    <select id="status" name="status" class="dune-select">
                        <option value="">Tous les statuts</option>
                        @foreach(\Azuriom\Plugin\DuneRp\Models\RpEvent::STATUSES as $statusKey => $statusName)
                            <option value="{{ $statusKey }}" {{ request('status') == $statusKey ? 'selected' : '' }}>
                                {{ $statusName }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                {{-- Event Type Filter --}}
                <div class="filter-group">
                    <label for="event_type" style="display: block; margin-bottom: 8px; color: var(--dune-sand); font-weight: bold;">
                        <i class="bi bi-tags"></i> Type
                    </label>
                    <select id="event_type" name="event_type" class="dune-select">
                        <option value="">Tous les types</option>
                        @foreach(\Azuriom\Plugin\DuneRp\Models\RpEvent::EVENT_TYPES as $typeKey => $typeName)
                            <option value="{{ $typeKey }}" {{ request('event_type') == $typeKey ? 'selected' : '' }}>
                                {{ $typeName }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                {{-- House Filter --}}
                <div class="filter-group">
                    <label for="house_id" style="display: block; margin-bottom: 8px; color: var(--dune-sand); font-weight: bold;">
                        <i class="bi bi-shield"></i> Maison
                    </label>
                    <select id="house_id" name="house_id" class="dune-select">
                        <option value="">Toutes les maisons</option>
                        @foreach($houses as $house)
                            <option value="{{ $house->id }}" {{ request('house_id') == $house->id ? 'selected' : '' }}>
                                {{ $house->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                {{-- Filter Actions --}}
                <div class="filter-actions" style="display: flex; gap: 10px;">
                    <button type="submit" class="dune-button">
                        <i class="bi bi-search"></i> Filtrer
                    </button>
                    <a href="{{ route('dune-rp.events.index') }}" class="dune-button secondary">
                        <i class="bi bi-arrow-clockwise"></i>
                    </a>
                </div>
            </form>
        </div>

        {{-- Quick Actions --}}
        <div class="quick-actions dune-panel" style="padding: 25px;">
            <h3 style="margin: 0 0 20px 0; color: var(--dune-spice-glow); font-size: 1.4rem;">
                <i class="bi bi-lightning-fill"></i> Actions Rapides
            </h3>
            
            <div style="display: flex; flex-direction: column; gap: 12px;">
                @auth
                    <a href="{{ route('dune-rp.events.create') }}" class="dune-button" style="width: 100%; text-align: center;">
                        <i class="bi bi-plus-circle"></i> {{ trans('dune-rp::messages.events.create') }}
                    </a>
                @else
                    <a href="{{ route('login') }}" class="dune-button" style="width: 100%; text-align: center;">
                        <i class="bi bi-box-arrow-in-right"></i> Se connecter pour créer
                    </a>
                @endauth
                
                <a href="{{ route('dune-rp.events.upcoming') }}" class="dune-button secondary" style="width: 100%; text-align: center;">
                    <i class="bi bi-calendar-plus"></i> {{ trans('dune-rp::messages.events.upcoming') }}
                </a>
                
                <a href="{{ route('dune-rp.events.calendar') }}" class="dune-button secondary" style="width: 100%; text-align: center;">
                    <i class="bi bi-calendar3"></i> {{ trans('dune-rp::messages.events.calendar') }}
                </a>
                
                <div class="view-toggle" style="display: flex; gap: 5px; border: 2px solid var(--dune-sand); border-radius: 25px; padding: 4px;">
                    <button onclick="switchView('cards')" id="cardsViewBtn" class="view-btn active" style="flex: 1; padding: 8px 12px; border: none; background: var(--dune-spice); color: white; border-radius: 20px; cursor: pointer; font-family: var(--font-dune); font-size: 0.9rem;">
                        <i class="bi bi-grid-3x3-gap"></i> Cartes
                    </button>
                    <button onclick="switchView('timeline')" id="timelineViewBtn" class="view-btn" style="flex: 1; padding: 8px 12px; border: none; background: transparent; color: var(--dune-sand); border-radius: 20px; cursor: pointer; font-family: var(--font-dune); font-size: 0.9rem;">
                        <i class="bi bi-list-timeline"></i> Timeline
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Events Display --}}
    @if($events->count() > 0)
        {{-- Cards View --}}
        <div id="eventsCards" class="events-cards" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 25px; margin-bottom: 40px;">
            @foreach($events as $event)
                <div class="event-card dune-panel fade-in" style="padding: 0; overflow: hidden; position: relative; border: 2px solid {{ $event->organizerHouse ? $event->organizerHouse->color : 'var(--dune-sand)' }};">
                    {{-- Event Header --}}
                    <div class="event-header" style="padding: 25px; background: linear-gradient(135deg, rgba(15,15,35,0.9), {{ $event->organizerHouse ? $event->organizerHouse->color.'40' : 'rgba(214,166,95,0.3)' }});">
                        <div style="display: flex; justify-content: between; align-items: start; margin-bottom: 15px;">
                            <div style="flex: 1;">
                                <h3 style="margin: 0 0 8px 0; color: var(--dune-spice-glow); font-size: 1.3rem;">
                                    {{ $event->title }}
                                </h3>
                                
                                {{-- Event Status and Type --}}
                                <div style="display: flex; gap: 8px; margin-bottom: 10px; flex-wrap: wrap;">
                                    <span class="status-badge status-{{ $event->status }}" style="padding: 4px 10px; border-radius: 12px; font-size: 0.7rem; font-weight: bold; text-transform: uppercase;">
                                        {{ $event->getStatusName() }}
                                    </span>
                                    <span class="type-badge type-{{ $event->event_type }}" style="padding: 4px 10px; border-radius: 12px; font-size: 0.7rem; font-weight: bold; background: {{ $event->getTypeColor() == 'warning' ? '#ff9800' : ($event->getTypeColor() == 'danger' ? '#f44336' : 'var(--dune-spice)') }}; color: white;">
                                        {{ $event->getEventTypeName() }}
                                    </span>
                                </div>
                            </div>
                            
                            {{-- Event Icon based on type --}}
                            <div style="font-size: 2.5rem; color: {{ $event->organizerHouse ? $event->organizerHouse->color : 'var(--dune-spice-glow)' }}; opacity: 0.3; margin-left: 15px;">
                                @switch($event->event_type)
                                    @case('harvest')
                                        <i class="bi bi-gem"></i>
                                        @break
                                    @case('combat')
                                        <i class="bi bi-sword"></i>
                                        @break
                                    @case('negotiation')
                                        <i class="bi bi-chat-dots"></i>
                                        @break
                                    @case('ceremony')
                                        <i class="bi bi-award"></i>
                                        @break
                                    @case('exploration')
                                        <i class="bi bi-compass"></i>
                                        @break
                                    @case('trade')
                                        <i class="bi bi-currency-exchange"></i>
                                        @break
                                    @case('council')
                                        <i class="bi bi-people"></i>
                                        @break
                                    @default
                                        <i class="bi bi-calendar-event"></i>
                                @endswitch
                            </div>
                        </div>
                        
                        {{-- Organizer Info --}}
                        <div style="display: flex; align-items: center; gap: 12px;">
                            @if($event->organizerHouse)
                                @if($event->organizerHouse->sigil_url)
                                    <img src="{{ $event->organizerHouse->getImageUrl() }}" alt="{{ $event->organizerHouse->name }}" style="width: 30px; height: 30px; border-radius: 50%; border: 2px solid {{ $event->organizerHouse->color }};">
                                @else
                                    <div style="width: 30px; height: 30px; border-radius: 50%; background: {{ $event->organizerHouse->color }}; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-shield" style="color: white; font-size: 0.8rem;"></i>
                                    </div>
                                @endif
                            @endif
                            
                            <div>
                                <div style="font-size: 0.9rem; color: var(--dune-sand);">
                                    <i class="bi bi-person"></i> {{ $event->organizer->name }}
                                    @if($event->organizerHouse)
                                        <span style="color: {{ $event->organizerHouse->color }};">• {{ $event->organizerHouse->name }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Event Details --}}
                    <div class="event-details" style="padding: 25px;">
                        {{-- Date and Location --}}
                        <div class="event-meta" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px; font-size: 0.9rem;">
                            <div>
                                <div style="color: var(--dune-sand-dark); margin-bottom: 3px;">
                                    <i class="bi bi-calendar"></i> Date & Heure
                                </div>
                                <div style="color: var(--dune-spice-glow); font-weight: bold;">
                                    {{ $event->getFormattedDate() }}
                                </div>
                                <div style="color: var(--dune-sand-dark); font-size: 0.8rem;">
                                    {{ $event->getRelativeTime() }}
                                </div>
                            </div>
                            
                            @if($event->location)
                                <div>
                                    <div style="color: var(--dune-sand-dark); margin-bottom: 3px;">
                                        <i class="bi bi-geo-alt"></i> Lieu
                                    </div>
                                    <div style="color: var(--dune-spice-glow); font-weight: bold;">
                                        {{ $event->location }}
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        {{-- Description --}}
                        @if($event->description)
                            <div class="event-description" style="margin-bottom: 15px;">
                                <div style="color: var(--dune-sand); line-height: 1.6; font-size: 0.95rem;">
                                    {{ Str::limit(strip_tags($event->parseDescription()), 120) }}
                                </div>
                            </div>
                        @endif
                        
                        {{-- Event Economics --}}
                        @if($event->spice_cost > 0 || $event->reward_spice > 0)
                            <div class="event-economics" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px; padding: 12px; background: rgba(230,126,34,0.1); border-radius: 8px; border-left: 4px solid var(--dune-spice);">
                                @if($event->spice_cost > 0)
                                    <div style="text-align: center;">
                                        <div style="color: var(--dune-sand-dark); font-size: 0.8rem; margin-bottom: 3px;">Coût</div>
                                        <div style="color: #f44336; font-weight: bold;">{{ number_format($event->spice_cost, 0) }} tonnes</div>
                                    </div>
                                @endif
                                
                                @if($event->reward_spice > 0)
                                    <div style="text-align: center;">
                                        <div style="color: var(--dune-sand-dark); font-size: 0.8rem; margin-bottom: 3px;">Récompense</div>
                                        <div class="spice-glow" style="font-weight: bold;">{{ number_format($event->reward_spice, 0) }} tonnes</div>
                                    </div>
                                @endif
                            </div>
                        @endif
                        
                        {{-- Participants Info --}}
                        @if($event->max_participants)
                            <div style="margin-bottom: 15px; font-size: 0.9rem;">
                                <i class="bi bi-people"></i>
                                <span style="color: var(--dune-sand);">Places limitées:</span>
                                <span style="color: var(--dune-spice-glow); font-weight: bold;">{{ $event->max_participants }} participants max</span>
                            </div>
                        @endif
                    </div>
                    
                    {{-- Event Actions --}}
                    <div class="event-actions" style="padding: 20px 25px; border-top: 1px solid rgba(214,166,95,0.2); background: rgba(0,0,0,0.1);">
                        <div style="display: flex; gap: 10px; justify-content: space-between; align-items: center;">
                            <a href="{{ route('dune-rp.events.show', $event) }}" class="dune-button" style="flex: 1; text-align: center; font-size: 0.9rem;">
                                <i class="bi bi-eye"></i> {{ trans('dune-rp::messages.common.details') }}
                            </a>
                            
                            @auth
                                @if($event->canUserParticipate(auth()->user()) && $event->isUpcoming())
                                    <button onclick="joinEvent({{ $event->id }})" class="dune-button secondary" style="flex: 1; font-size: 0.9rem;" data-event-id="{{ $event->id }}">
                                        <i class="bi bi-plus-circle"></i> {{ trans('dune-rp::messages.events.join') }}
                                    </button>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Timeline View --}}
        <div id="eventsTimeline" class="events-timeline" style="display: none; margin-bottom: 40px;">
            <div class="timeline-container" style="position: relative; max-width: 1000px; margin: 0 auto;">
                {{-- Timeline Line --}}
                <div class="timeline-line" style="position: absolute; left: 50%; top: 0; bottom: 0; width: 4px; background: linear-gradient(180deg, var(--dune-spice), var(--dune-spice-glow)); transform: translateX(-50%); border-radius: 2px;"></div>
                
                @foreach($events->sortBy('event_date') as $index => $event)
                    <div class="timeline-item" style="position: relative; margin: 40px 0; {{ $index % 2 == 0 ? 'padding-right: 55%; text-align: right;' : 'padding-left: 55%;' }}">
                        {{-- Timeline Dot --}}
                        <div class="timeline-dot" style="position: absolute; left: 50%; top: 20px; width: 20px; height: 20px; background: {{ $event->organizerHouse ? $event->organizerHouse->color : 'var(--dune-spice)' }}; border-radius: 50%; transform: translateX(-50%); border: 4px solid var(--dune-space); box-shadow: 0 0 10px rgba(214,166,95,0.5);"></div>
                        
                        {{-- Event Card --}}
                        <div class="timeline-card dune-panel" style="padding: 20px; border: 2px solid {{ $event->organizerHouse ? $event->organizerHouse->color : 'var(--dune-sand)' }};">
                            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                                <h4 style="margin: 0; color: var(--dune-spice-glow); font-size: 1.1rem;">{{ $event->title }}</h4>
                                <span class="status-badge status-{{ $event->status }}" style="padding: 3px 8px; border-radius: 10px; font-size: 0.6rem; font-weight: bold;">
                                    {{ $event->getStatusName() }}
                                </span>
                            </div>
                            
                            <div style="color: var(--dune-sand); font-size: 0.9rem; margin-bottom: 8px;">
                                <i class="bi bi-calendar"></i> {{ $event->getFormattedDate() }}
                                @if($event->location)
                                    <i class="bi bi-geo-alt" style="margin-left: 15px;"></i> {{ $event->location }}
                                @endif
                            </div>
                            
                            @if($event->organizerHouse)
                                <div style="color: {{ $event->organizerHouse->color }}; font-size: 0.8rem; margin-bottom: 10px;">
                                    <i class="bi bi-shield"></i> {{ $event->organizerHouse->name }}
                                </div>
                            @endif
                            
                            @if($event->description)
                                <p style="margin: 0 0 10px 0; font-size: 0.9rem; color: var(--dune-sand);">
                                    {{ Str::limit(strip_tags($event->parseDescription()), 100) }}
                                </p>
                            @endif
                            
                            <a href="{{ route('dune-rp.events.show', $event) }}" class="dune-button secondary" style="font-size: 0.8rem; padding: 6px 12px;">
                                <i class="bi bi-eye"></i> Voir
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Pagination --}}
        @if($events->hasPages())
            <div class="pagination-wrapper" style="display: flex; justify-content: center; margin-top: 30px;">
                {{ $events->appends(request()->query())->links() }}
            </div>
        @endif
    @else
        <div class="empty-events dune-panel" style="text-align: center; padding: 60px 20px;">
            <i class="bi bi-calendar-x" style="font-size: 4rem; color: var(--dune-sand-dark); margin-bottom: 20px;"></i>
            <h3 style="color: var(--dune-sand); margin-bottom: 15px;">Aucun Événement Trouvé</h3>
            <p style="color: var(--dune-sand); margin-bottom: 25px;">
                @if(request()->hasAny(['status', 'event_type', 'house_id']))
                    Aucun événement ne correspond à vos critères de recherche.
                @else
                    Aucun événement n'est actuellement planifié. Soyez le premier à créer un événement !
                @endif
            </p>
            
            <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                @if(request()->hasAny(['status', 'event_type', 'house_id']))
                    <a href="{{ route('dune-rp.events.index') }}" class="dune-button secondary">
                        <i class="bi bi-arrow-clockwise"></i> Réinitialiser
                    </a>
                @endif
                
                @auth
                    <a href="{{ route('dune-rp.events.create') }}" class="dune-button">
                        <i class="bi bi-plus-circle"></i> {{ trans('dune-rp::messages.events.create') }}
                    </a>
                @else
                    <a href="{{ route('login') }}" class="dune-button">
                        <i class="bi bi-box-arrow-in-right"></i> Se connecter
                    </a>
                @endauth
            </div>
        </div>
    @endif
</div>

<style>
/* Status badge colors */
.status-badge.status-planned { background: #2196f3; color: white; }
.status-badge.status-ongoing { background: #ff9800; color: white; }
.status-badge.status-completed { background: #4caf50; color: white; }
.status-badge.status-cancelled { background: #f44336; color: white; }

/* View toggle active state */
.view-btn.active {
    background: var(--dune-spice) !important;
    color: white !important;
}

/* Animation delays for staggered appearance */
.event-card:nth-child(1) { animation-delay: 0.1s; }
.event-card:nth-child(2) { animation-delay: 0.2s; }
.event-card:nth-child(3) { animation-delay: 0.3s; }
.event-card:nth-child(4) { animation-delay: 0.4s; }
.event-card:nth-child(5) { animation-delay: 0.5s; }
.event-card:nth-child(6) { animation-delay: 0.6s; }

/* Hover effects */
.event-card {
    transition: all 0.3s ease;
}

.event-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 35px rgba(214, 166, 95, 0.3);
}

.timeline-card {
    transition: all 0.3s ease;
}

.timeline-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(214, 166, 95, 0.3);
}

/* Timeline animations */
.timeline-item {
    animation: slideInFromSide 0.6s ease-out;
}

.timeline-item:nth-child(odd) {
    animation-name: slideInFromLeft;
}

.timeline-item:nth-child(even) {
    animation-name: slideInFromRight;
}

@keyframes slideInFromLeft {
    from {
        opacity: 0;
        transform: translateX(-50px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes slideInFromRight {
    from {
        opacity: 0;
        transform: translateX(50px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Responsive design */
@media (max-width: 1024px) {
    .events-controls {
        grid-template-columns: 1fr !important;
    }
    
    .events-cards {
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)) !important;
    }
}

@media (max-width: 768px) {
    .events-cards {
        grid-template-columns: 1fr !important;
    }
    
    .filter-form {
        grid-template-columns: 1fr !important;
    }
    
    .events-header {
        padding: 30px 15px !important;
    }
    
    .events-header h1 {
        font-size: 2rem !important;
    }
    
    .events-stats {
        gap: 15px !important;
    }
    
    .event-meta {
        grid-template-columns: 1fr !important;
        gap: 10px !important;
    }
    
    .event-economics {
        grid-template-columns: 1fr !important;
    }
    
    .timeline-item {
        padding-left: 80px !important;
        padding-right: 20px !important;
        text-align: left !important;
    }
    
    .timeline-line {
        left: 30px !important;
    }
    
    .timeline-dot {
        left: 30px !important;
    }
}
</style>

<script>
// View switching
function switchView(viewType) {
    const cardsView = document.getElementById('eventsCards');
    const timelineView = document.getElementById('eventsTimeline');
    const cardsBtn = document.getElementById('cardsViewBtn');
    const timelineBtn = document.getElementById('timelineViewBtn');
    
    if (viewType === 'cards') {
        cardsView.style.display = 'grid';
        timelineView.style.display = 'none';
        cardsBtn.classList.add('active');
        timelineBtn.classList.remove('active');
    } else {
        cardsView.style.display = 'none';
        timelineView.style.display = 'block';
        timelineBtn.classList.add('active');
        cardsBtn.classList.remove('active');
    }
    
    // Save preference
    localStorage.setItem('dune-rp-events-view', viewType);
}

// Load saved view preference
document.addEventListener('DOMContentLoaded', function() {
    const savedView = localStorage.getItem('dune-rp-events-view');
    if (savedView && savedView === 'timeline') {
        switchView('timeline');
    }
});

// Auto-submit form on filter change
document.addEventListener('DOMContentLoaded', function() {
    const autoSubmitFields = ['status', 'event_type', 'house_id'];
    autoSubmitFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('change', function() {
                this.form.submit();
            });
        }
    });
});

// Join event functionality
@auth
async function joinEvent(eventId) {
    const button = document.querySelector(`[data-event-id="${eventId}"]`);
    if (!button) return;
    
    button.disabled = true;
    button.innerHTML = '<i class="bi bi-hourglass-split"></i> En cours...';
    
    try {
        const response = await fetch(`/dune-rp/events/${eventId}/join`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification('success', data.message || 'Vous participez maintenant à cet événement !');
            button.innerHTML = '<i class="bi bi-check"></i> Inscrit';
            button.classList.remove('dune-button');
            button.classList.add('dune-button', 'secondary');
        } else {
            showNotification('error', data.message || 'Impossible de rejoindre cet événement.');
            button.disabled = false;
            button.innerHTML = '<i class="bi bi-plus-circle"></i> {{ trans("dune-rp::messages.events.join") }}';
        }
    } catch (error) {
        showNotification('error', 'Erreur de connexion. Veuillez réessayer.');
        button.disabled = false;
        button.innerHTML = '<i class="bi bi-plus-circle"></i> {{ trans("dune-rp::messages.events.join") }}';
    }
}

function showNotification(type, message) {
    const notification = document.createElement('div');
    notification.className = `dune-alert ${type}`;
    notification.innerHTML = `<i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i> ${message}`;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 2000;
        max-width: 400px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        animation: slideInRight 0.3s ease-out;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease-in forwards';
        setTimeout(() => notification.remove(), 300);
    }, 4000);
}

// Add animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);
@endauth

// Countdown timers for upcoming events
function updateCountdowns() {
    document.querySelectorAll('[data-countdown]').forEach(element => {
        const eventDate = new Date(element.dataset.countdown);
        const now = new Date();
        const diff = eventDate - now;
        
        if (diff > 0) {
            const days = Math.floor(diff / (1000 * 60 * 60 * 24));
            const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            
            if (days > 0) {
                element.textContent = `Dans ${days} jour${days > 1 ? 's' : ''}`;
            } else if (hours > 0) {
                element.textContent = `Dans ${hours} heure${hours > 1 ? 's' : ''}`;
            } else {
                element.textContent = `Dans ${minutes} minute${minutes > 1 ? 's' : ''}`;
            }
        } else {
            element.textContent = 'En cours';
            element.style.color = '#ff9800';
        }
    });
}

// Update countdowns every minute
setInterval(updateCountdowns, 60000);
updateCountdowns(); // Initial call
</script>
@endsection

@push('scripts')
    <script src="{{ plugin_asset('dune-rp', 'js/dune-rp.js') }}"></script>
@endpush
