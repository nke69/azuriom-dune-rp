@extends('layouts.app')

@section('title', trans('dune-rp::messages.title'))

@push('styles')
    <link href="{{ plugin_asset('dune-rp', 'css/dune-rp.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="dune-container">
    {{-- Hero Section --}}
    <div class="hero-section dune-panel" style="text-align: center; padding: 60px 20px; background: linear-gradient(135deg, rgba(15,15,35,0.9), rgba(139,0,0,0.3)); border: none; margin-bottom: 40px;">
        <h1 class="dune-heading" style="font-size: 3rem; margin-bottom: 20px;">
            {{ trans('dune-rp::messages.home.welcome') }}
        </h1>
        <p style="font-size: 1.2rem; color: var(--dune-sand); margin-bottom: 30px; max-width: 800px; margin-left: auto; margin-right: auto;">
            {{ trans('dune-rp::messages.home.subtitle') }}
        </p>
        
        <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
            <a href="{{ route('dune-rp.characters.create') }}" class="dune-button" style="font-size: 1.1rem; padding: 15px 30px;">
                <i class="bi bi-person-plus"></i> {{ trans('dune-rp::messages.home.create_character') }}
            </a>
            <a href="{{ route('dune-rp.houses.index') }}" class="dune-button secondary" style="font-size: 1.1rem; padding: 15px 30px;">
                <i class="bi bi-shield"></i> {{ trans('dune-rp::messages.home.view_houses') }}
            </a>
        </div>
    </div>

    {{-- Statistics Section --}}
    <div class="stats-section" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 40px;">
        <div class="stat-card dune-panel" style="text-align: center;">
            <div class="admin-stat-number spice-glow">{{ number_format($statistics['total_houses']) }}</div>
            <div class="stat-label">{{ trans('dune-rp::messages.houses.title') }}</div>
        </div>
        
        <div class="stat-card dune-panel" style="text-align: center;">
            <div class="admin-stat-number spice-glow">{{ number_format($statistics['total_characters']) }}</div>
            <div class="stat-label">{{ trans('dune-rp::messages.characters.title') }}</div>
        </div>
        
        <div class="stat-card dune-panel" style="text-align: center;">
            <div class="admin-stat-number spice-glow" data-auto-refresh="spice">{{ number_format($statistics['total_spice'], 0) }}</div>
            <div class="stat-label">{{ trans('dune-rp::messages.home.total_spice') }}</div>
        </div>
        
        <div class="stat-card dune-panel" style="text-align: center;">
            <div class="admin-stat-number spice-glow">{{ number_format($statistics['active_events']) }}</div>
            <div class="stat-label">{{ trans('dune-rp::messages.home.active_events') }}</div>
        </div>
    </div>

    <div class="main-content" style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
        {{-- Top Houses --}}
        <div class="top-houses-section">
            <h2 class="dune-heading" style="margin-bottom: 25px;">
                <i class="bi bi-trophy"></i> {{ trans('dune-rp::messages.home.top_houses') }}
            </h2>
            
            @if($houses->count() > 0)
                <div class="houses-list" style="display: flex; flex-direction: column; gap: 15px;">
                    @foreach($houses as $index => $house)
                        <div class="house-item dune-panel" style="display: flex; align-items: center; padding: 20px;">
                            <div class="house-rank" style="font-size: 1.5rem; font-weight: bold; color: var(--dune-spice-glow); width: 40px;">
                                #{{ $index + 1 }}
                            </div>
                            
                            @if($house->sigil_url)
                                <div class="house-sigil" style="width: 60px; height: 60px; margin-right: 20px; background-image: url('{{ $house->getImageUrl() }}');">
                                </div>
                            @else
                                <div class="house-sigil" style="width: 60px; height: 60px; margin-right: 20px; background: var(--dune-sand); display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-shield" style="font-size: 1.5rem; color: var(--dune-space);"></i>
                                </div>
                            @endif
                            
                            <div class="house-info" style="flex: 1;">
                                <h4 style="margin: 0 0 5px 0; color: var(--dune-spice-glow);">{{ $house->name }}</h4>
                                @if($house->motto)
                                    <p style="margin: 0 0 10px 0; font-style: italic; color: var(--dune-sand); font-size: 0.9rem;">
                                        "{{ $house->motto }}"
                                    </p>
                                @endif
                                
                                <div class="house-quick-stats" style="display: flex; gap: 20px; font-size: 0.9rem;">
                                    <span>
                                        <i class="bi bi-people"></i> {{ $house->active_characters_count }} {{ trans('dune-rp::messages.common.members') }}
                                    </span>
                                    <span class="spice-glow">
                                        <i class="bi bi-gem"></i> {{ number_format($house->influence_points) }}
                                    </span>
                                </div>
                            </div>
                            
                            <a href="{{ route('dune-rp.houses.show', $house) }}" class="dune-button secondary" style="padding: 8px 16px;">
                                {{ trans('dune-rp::messages.common.view') }}
                            </a>
                        </div>
                    @endforeach
                </div>
                
                <div style="text-align: center; margin-top: 20px;">
                    <a href="{{ route('dune-rp.houses.index') }}" class="dune-button">
                        {{ trans('dune-rp::messages.houses.view_all') }}
                    </a>
                </div>
            @else
                <div class="empty-state dune-panel" style="text-align: center; padding: 40px;">
                    <i class="bi bi-shield" style="font-size: 3rem; color: var(--dune-sand-dark); margin-bottom: 15px;"></i>
                    <p>{{ trans('dune-rp::messages.houses.no_houses') }}</p>
                </div>
            @endif
        </div>

        {{-- Upcoming Events & Recent Characters --}}
        <div class="events-characters-section">
            {{-- Upcoming Events --}}
            <div class="upcoming-events" style="margin-bottom: 30px;">
                <h2 class="dune-heading" style="margin-bottom: 25px;">
                    <i class="bi bi-calendar-event"></i> {{ trans('dune-rp::messages.home.upcoming_events') }}
                </h2>
                
                @if($upcomingEvents->count() > 0)
                    <div class="events-list" style="display: flex; flex-direction: column; gap: 15px;">
                        @foreach($upcomingEvents as $event)
                            <div class="event-item dune-panel" style="padding: 15px;">
                                <div class="event-header" style="display: flex; justify-content: between; align-items: start; margin-bottom: 10px;">
                                    <h5 style="margin: 0; color: var(--dune-spice-glow);">{{ $event->title }}</h5>
                                    <span class="event-type-badge">{{ $event->getEventTypeName() }}</span>
                                </div>
                                
                                <div class="event-meta" style="display: flex; gap: 15px; font-size: 0.9rem; color: var(--dune-sand); margin-bottom: 10px;">
                                    <span>
                                        <i class="bi bi-clock"></i> {{ $event->getFormattedDate() }}
                                    </span>
                                    @if($event->location)
                                        <span>
                                            <i class="bi bi-geo-alt"></i> {{ $event->location }}
                                        </span>
                                    @endif
                                    @if($event->organizerHouse)
                                        <span>
                                            <i class="bi bi-shield"></i> {{ $event->organizerHouse->name }}
                                        </span>
                                    @endif
                                </div>
                                
                                @if($event->description)
                                    <p style="margin: 0 0 10px 0; font-size: 0.9rem;">{{ Str::limit($event->description, 100) }}</p>
                                @endif
                                
                                <a href="{{ route('dune-rp.events.show', $event) }}" class="dune-button secondary" style="padding: 6px 12px; font-size: 0.9rem;">
                                    {{ trans('dune-rp::messages.common.details') }}
                                </a>
                            </div>
                        @endforeach
                    </div>
                    
                    <div style="text-align: center; margin-top: 15px;">
                        <a href="{{ route('dune-rp.events.index') }}" class="dune-button">
                            {{ trans('dune-rp::messages.events.view_all') }}
                        </a>
                    </div>
                @else
                    <div class="empty-state dune-panel" style="text-align: center; padding: 30px;">
                        <i class="bi bi-calendar-x" style="font-size: 2.5rem; color: var(--dune-sand-dark); margin-bottom: 10px;"></i>
                        <p>{{ trans('dune-rp::messages.events.no_events') }}</p>
                    </div>
                @endif
            </div>

            {{-- Recent Characters --}}
            <div class="recent-characters">
                <h2 class="dune-heading" style="margin-bottom: 25px;">
                    <i class="bi bi-people"></i> {{ trans('dune-rp::messages.home.recent_characters') }}
                </h2>
                
                @if($recentCharacters->count() > 0)
                    <div class="characters-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px;">
                        @foreach($recentCharacters as $character)
                            <div class="character-mini-card dune-panel" style="text-align: center; padding: 15px;">
                                <div class="character-avatar" style="width: 70px; height: 70px; margin: 0 auto 10px;">
                                    @if($character->avatar_url)
                                        <img src="{{ $character->getImageUrl() }}" alt="{{ $character->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                    @else
                                        <div style="width: 100%; height: 100%; background: var(--dune-sand); display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                            <i class="bi bi-person" style="font-size: 1.8rem; color: var(--dune-space);"></i>
                                        </div>
                                    @endif
                                </div>
                                
                                <h6 style="margin: 0 0 5px 0; color: var(--dune-spice-glow); font-size: 0.9rem;">{{ $character->name }}</h6>
                                @if($character->house)
                                    <p style="margin: 0 0 10px 0; font-size: 0.8rem; color: var(--dune-sand);">{{ $character->house->name }}</p>
                                @endif
                                
                                <a href="{{ route('dune-rp.characters.show', $character) }}" class="dune-button secondary" style="padding: 4px 8px; font-size: 0.8rem;">
                                    {{ trans('dune-rp::messages.common.view') }}
                                </a>
                            </div>
                        @endforeach
                    </div>
                    
                    <div style="text-align: center; margin-top: 15px;">
                        <a href="{{ route('dune-rp.characters.index') }}" class="dune-button">
                            {{ trans('dune-rp::messages.characters.view_all') }}
                        </a>
                    </div>
                @else
                    <div class="empty-state dune-panel" style="text-align: center; padding: 30px;">
                        <i class="bi bi-person-x" style="font-size: 2.5rem; color: var(--dune-sand-dark); margin-bottom: 10px;"></i>
                        <p>{{ trans('dune-rp::messages.characters.no_characters') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Call to Action --}}
    @guest
        <div class="cta-section dune-panel" style="text-align: center; padding: 40px; margin-top: 40px; background: linear-gradient(45deg, rgba(139,0,0,0.2), rgba(30,74,140,0.2));">
            <h3 class="dune-heading" style="margin-bottom: 20px;">{{ trans('dune-rp::messages.home.join_adventure') }}</h3>
            <p style="margin-bottom: 25px; font-size: 1.1rem;">Créez votre compte pour commencer votre aventure dans l'univers de Dune !</p>
            
            <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                <a href="{{ route('register') }}" class="dune-button" style="font-size: 1.1rem; padding: 12px 25px;">
                    <i class="bi bi-person-plus"></i> S'inscrire
                </a>
                <a href="{{ route('login') }}" class="dune-button secondary" style="font-size: 1.1rem; padding: 12px 25px;">
                    <i class="bi bi-box-arrow-in-right"></i> Se connecter
                </a>
            </div>
        </div>
    @endguest
</div>

{{-- Add responsive styling --}}
<style>
@media (max-width: 768px) {
    .main-content {
        grid-template-columns: 1fr !important;
    }
    
    .house-item {
        flex-direction: column !important;
        text-align: center !important;
    }
    
    .house-info {
        margin: 15px 0 !important;
    }
    
    .house-quick-stats {
        justify-content: center !important;
    }
    
    .hero-section h1 {
        font-size: 2rem !important;
    }
    
    .stats-section {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)) !important;
    }
}
</style>
@endsection

@push('scripts')
    <script src="{{ plugin_asset('dune-rp', 'js/dune-rp.js') }}"></script>
@endpush
