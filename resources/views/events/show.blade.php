```blade
@extends('layouts.app')

@section('title', $event->title . ' - ' . trans('dune-rp::messages.events.title'))

@push('styles')
<style>
:root {
    --dune-sand: #D6A65F;
    --dune-sand-dark: #B8935A;
    --dune-spice: #FF8C42;
    --dune-spice-glow: #FFA500;
    --dune-desert: #8B4513;
    --dune-night: #0F0F23;
    --font-dune: 'Cinzel', serif;
}

.event-hero {
    background: linear-gradient(135deg, var(--dune-night), var(--dune-desert));
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    margin-bottom: 30px;
}

.event-header {
    padding: 40px;
    background: linear-gradient(45deg, rgba(214,166,95,0.1), rgba(255,140,66,0.1));
    position: relative;
}

.event-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(circle at 30% 70%, rgba(255,165,0,0.2), transparent 50%);
    pointer-events: none;
}

.status-badge {
    display: inline-block;
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: bold;
    text-transform: uppercase;
    font-size: 0.8rem;
    margin-bottom: 15px;
}

.status-planned { background: #2196F3; color: white; }
.status-ongoing { background: #4CAF50; color: white; }
.status-completed { background: #9E9E9E; color: white; }
.status-cancelled { background: #F44336; color: white; }

.type-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 15px;
    font-size: 0.7rem;
    font-weight: bold;
    margin-left: 10px;
}

.dune-panel {
    background: linear-gradient(145deg, rgba(15,15,35,0.95), rgba(139,69,19,0.1));
    border: 2px solid var(--dune-sand);
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 25px;
    color: var(--dune-sand);
    box-shadow: 0 8px 25px rgba(0,0,0,0.3);
}

.participant-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 2px solid var(--dune-sand);
    margin-right: 10px;
}

.dune-button {
    background: linear-gradient(45deg, var(--dune-spice), var(--dune-spice-glow));
    border: none;
    color: white;
    padding: 12px 24px;
    border-radius: 25px;
    font-family: var(--font-dune);
    font-weight: bold;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(255,140,66,0.4);
}

.dune-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255,140,66,0.6);
    color: white;
    text-decoration: none;
}

.dune-button.secondary {
    background: linear-gradient(45deg, var(--dune-sand-dark), var(--dune-sand));
    box-shadow: 0 4px 15px rgba(214,166,95,0.4);
}

.dune-button.danger {
    background: linear-gradient(45deg, #dc3545, #c82333);
    box-shadow: 0 4px 15px rgba(220,53,69,0.4);
}

@media (max-width: 768px) {
    .event-header {
        padding: 25px 20px;
    }
    
    .dune-panel {
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .event-meta {
        grid-template-columns: 1fr !important;
        gap: 15px !important;
    }
    
    .participant-list {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)) !important;
    }
}
</style>
@endpush

@section('content')
<div class="container">
    {{-- Event Hero Section --}}
    <div class="event-hero">
        <div class="event-header" style="border-bottom: 2px solid {{ $event->organizerHouse ? $event->organizerHouse->color : 'var(--dune-sand)' }};">
            {{-- Status and Type Badges --}}
            <div style="margin-bottom: 20px;">
                <span class="status-badge status-{{ $event->status }}">
                    {{ $event->getStatusName() }}
                </span>
                <span class="type-badge" style="background: {{ $event->getTypeColor() == 'warning' ? '#ff9800' : ($event->getTypeColor() == 'danger' ? '#f44336' : '#4caf50') }}; color: white;">
                    {{ $event->getTypeName() }}
                </span>
            </div>

            {{-- Event Title --}}
            <h1 style="color: var(--dune-spice-glow); font-family: var(--font-dune); font-size: 2.5rem; margin: 0 0 20px 0; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">
                {{ $event->title }}
            </h1>

            {{-- Organizer Info --}}
            <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 25px;">
                @if($event->organizerHouse)
                    @if($event->organizerHouse->getImageUrl())
                        <img src="{{ $event->organizerHouse->getImageUrl() }}" alt="{{ $event->organizerHouse->name }}" style="width: 50px; height: 50px; border-radius: 50%; border: 3px solid {{ $event->organizerHouse->color }};">
                    @else
                        <div style="width: 50px; height: 50px; border-radius: 50%; background: {{ $event->organizerHouse->color }}; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-shield" style="color: white; font-size: 1.2rem;"></i>
                        </div>
                    @endif
                @endif
                
                <div>
                    <div style="color: var(--dune-sand); font-size: 1.1rem; margin-bottom: 5px;">
                        <i class="bi bi-person-badge"></i> {{ trans('dune-rp::messages.events.organized_by') }}
                    </div>
                    <div style="color: white; font-size: 1.3rem; font-weight: bold;">
                        {{ $event->organizer->name }}
                        @if($event->organizerHouse)
                            <span style="color: {{ $event->organizerHouse->color }};">• {{ $event->organizerHouse->name }}</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            @auth
                <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                    @if($event->status === 'planned' && $event->event_date > now())
                        @if($event->isUserParticipating(auth()->id()))
                            <form action="{{ route('dune-rp.events.leave', $event) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="dune-button danger">
                                    <i class="bi bi-box-arrow-left"></i> {{ trans('dune-rp::messages.events.leave') }}
                                </button>
                            </form>
                        @else
                            @if(!$event->max_participants || $event->participants()->count() < $event->max_participants)
                                <form action="{{ route('dune-rp.events.join', $event) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="dune-button">
                                        <i class="bi bi-person-plus"></i> {{ trans('dune-rp::messages.events.join') }}
                                    </button>
                                </form>
                            @endif
                        @endif
                    @endif
                    
                    @if($event->organizer_id === auth()->id() || auth()->user()->can('dune-rp.events.manage'))
                        <a href="{{ route('dune-rp.events.edit', $event) }}" class="dune-button secondary">
                            <i class="bi bi-pencil"></i> {{ trans('dune-rp::messages.events.edit') }}
                        </a>
                    @endif
                </div>
            @endauth
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            {{-- Event Details --}}
            <div class="dune-panel">
                <h3 style="color: var(--dune-spice-glow); font-family: var(--font-dune); margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                    <i class="bi bi-info-circle"></i> {{ trans('dune-rp::messages.events.details') }}
                </h3>

                {{-- Event Meta Info --}}
                <div class="event-meta" style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-bottom: 25px;">
                    <div>
                        <div style="color: var(--dune-sand-dark); margin-bottom: 8px; font-size: 0.9rem;">
                            <i class="bi bi-calendar-event"></i> {{ trans('dune-rp::messages.events.date_time') }}
                        </div>
                        <div style="color: white; font-size: 1.1rem; font-weight: bold;">
                            {{ $event->event_date->format('d/m/Y à H:i') }}
                        </div>
                        <div style="color: var(--dune-sand); font-size: 0.9rem;">
                            {{ $event->event_date->diffForHumans() }}
                        </div>
                    </div>

                    @if($event->location)
                        <div>
                            <div style="color: var(--dune-sand-dark); margin-bottom: 8px; font-size: 0.9rem;">
                                <i class="bi bi-geo-alt"></i> {{ trans('dune-rp::messages.events.location') }}
                            </div>
                            <div style="color: white; font-size: 1.1rem;">
                                {{ $event->location }}
                            </div>
                        </div>
                    @endif

                    @if($event->max_participants)
                        <div>
                            <div style="color: var(--dune-sand-dark); margin-bottom: 8px; font-size: 0.9rem;">
                                <i class="bi bi-people"></i> {{ trans('dune-rp::messages.events.participants') }}
                            </div>
                            <div style="color: white; font-size: 1.1rem;">
                                {{ $event->participants()->count() }} / {{ $event->max_participants }}
                            </div>
                        </div>
                    @endif

                    @if($event->spice_cost > 0)
                        <div>
                            <div style="color: var(--dune-sand-dark); margin-bottom: 8px; font-size: 0.9rem;">
                                <i class="bi bi-coin"></i> {{ trans('dune-rp::messages.events.cost') }}
                            </div>
                            <div style="color: var(--dune-spice-glow); font-size: 1.1rem; font-weight: bold;">
                                {{ number_format($event->spice_cost) }} {{ trans('dune-rp::messages.spice.unit') }}
                            </div>
                        </div>
                    @endif

                    @if($event->reward_spice > 0)
                        <div>
                            <div style="color: var(--dune-sand-dark); margin-bottom: 8px; font-size: 0.9rem;">
                                <i class="bi bi-trophy"></i> {{ trans('dune-rp::messages.events.reward') }}
                            </div>
                            <div style="color: var(--dune-spice-glow); font-size: 1.1rem; font-weight: bold;">
                                {{ number_format($event->reward_spice) }} {{ trans('dune-rp::messages.spice.unit') }}
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Event Description --}}
                <div style="border-top: 1px solid var(--dune-sand); padding-top: 20px;">
                    <h4 style="color: var(--dune-spice-glow); margin-bottom: 15px;">
                        {{ trans('dune-rp::messages.events.description') }}
                    </h4>
                    <div style="color: var(--dune-sand); line-height: 1.6; font-size: 1.05rem;">
                        {!! nl2br(e($event->description)) !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- Participants --}}
            @if($event->participants()->count() > 0)
                <div class="dune-panel">
                    <h3 style="color: var(--dune-spice-glow); font-family: var(--font-dune); margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                        <i class="bi bi-people-fill"></i> {{ trans('dune-rp::messages.events.participants') }}
                        <span style="background: var(--dune-spice); color: white; padding: 4px 8px; border-radius: 12px; font-size: 0.8rem; margin-left: auto;">
                            {{ $event->participants()->count() }}
                        </span>
                    </h3>

                    <div class="participant-list" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                        @foreach($event->participants as $participant)
                            <div style="display: flex; align-items: center; background: rgba(214,166,95,0.1); padding: 12px; border-radius: 8px; border-left: 4px solid {{ $participant->house ? $participant->house->color : 'var(--dune-sand)' }};">
                                @if($participant->user->getAvatar())
                                    <img src="{{ $participant->user->getAvatar() }}" alt="{{ $participant->user->name }}" class="participant-avatar">
                                @else
                                    <div class="participant-avatar" style="background: var(--dune-spice); display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-person" style="color: white;"></i>
                                    </div>
                                @endif

                                <div style="flex: 1;">
                                    <div style="color: white; font-weight: bold;">
                                        {{ $participant->user->name }}
                                    </div>
                                    @if($participant->house)
                                        <div style="color: {{ $participant->house->color }}; font-size: 0.9rem;">
                                            {{ $participant->house->name }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Event Stats --}}
            <div class="dune-panel">
                <h3 style="color: var(--dune-spice-glow); font-family: var(--font-dune); margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                    <i class="bi bi-bar-chart"></i> {{ trans('dune-rp::messages.events.statistics') }}
                </h3>

                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <div style="display: flex; justify-content: between; align-items: center;">
                        <span style="color: var(--dune-sand);">{{ trans('dune-rp::messages.events.created') }}</span>
                        <span style="color: white;">{{ $event->created_at->format('d/m/Y') }}</span>
                    </div>

                    <div style="display: flex; justify-content: between; align-items: center;">
                        <span style="color: var(--dune-sand);">{{ trans('dune-rp::messages.events.participants') }}</span>
                        <span style="color: var(--dune-spice-glow); font-weight: bold;">{{ $event->participants()->count() }}</span>
                    </div>

                    @if($event->spice_cost > 0)
                        <div style="display: flex; justify-content: between; align-items: center;">
                            <span style="color: var(--dune-sand);">{{ trans('dune-rp::messages.spice.total_cost') }}</span>
                            <span style="color: var(--dune-spice-glow); font-weight: bold;">
                                {{ number_format($event->spice_cost * $event->participants()->count()) }} {{ trans('dune-rp::messages.spice.unit') }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Related Events --}}
            @if($relatedEvents ?? false)
                <div class="dune-panel">
                    <h3 style="color: var(--dune-spice-glow); font-family: var(--font-dune); margin-bottom: 20px;">
                        <i class="bi bi-link-45deg"></i> {{ trans('dune-rp::messages.events.related') }}
                    </h3>

                    @foreach($relatedEvents as $relatedEvent)
                        <a href="{{ route('dune-rp.events.show', $relatedEvent) }}" style="display: block; color: var(--dune-sand); text-decoration: none; padding: 10px; border-radius: 8px; background: rgba(214,166,95,0.1); margin-bottom: 10px; transition: all 0.3s;">
                            <div style="font-weight: bold; margin-bottom: 5px;">{{ $relatedEvent->title }}</div>
                            <div style="font-size: 0.9rem; color: var(--dune-sand-dark);">
                                {{ $relatedEvent->event_date->format('d/m/Y') }} • {{ $relatedEvent->getStatusName() }}
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Back Button --}}
    <div style="text-align: center; margin-top: 30px;">
        <a href="{{ route('dune-rp.events.index') }}" class="dune-button secondary">
            <i class="bi bi-arrow-left"></i> {{ trans('dune-rp::messages.events.back_to_list') }}
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-refresh for ongoing events
@if($event->status === 'ongoing')
setInterval(function() {
    location.reload();
}, 300000); // Refresh every 5 minutes
@endif

// Spice effect animation
document.addEventListener('DOMContentLoaded', function() {
    const spiceElements = document.querySelectorAll('[style*="var(--dune-spice-glow)"]');
    
    spiceElements.forEach(element => {
        element.addEventListener('mouseover', function() {
            this.style.textShadow = '0 0 10px var(--dune-spice-glow), 0 0 20px var(--dune-spice-glow), 0 0 30px var(--dune-spice-glow)';
        });
        
        element.addEventListener('mouseout', function() {
            this.style.textShadow = '2px 2px 4px rgba(0,0,0,0.5)';
        });
    });
});
</script>
@endpush
```
