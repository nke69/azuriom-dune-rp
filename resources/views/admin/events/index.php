```blade
@extends('admin.layouts.admin')

@section('title', trans('dune-rp::admin.events.title'))

@push('styles')
<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: linear-gradient(135deg, #fff, #f8f9fa);
    border-left: 4px solid #007bff;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.stat-card.planned { border-left-color: #007bff; }
.stat-card.ongoing { border-left-color: #28a745; }
.stat-card.completed { border-left-color: #6c757d; }
.stat-card.cancelled { border-left-color: #dc3545; }

.stat-number {
    font-size: 2.5rem;
    font-weight: bold;
    margin-bottom: 8px;
    color: #495057;
}

.stat-label {
    color: #6c757d;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.filters-card {
    background: #fff;
    border-radius: 8px;
    padding: 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 25px;
}

.filter-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    align-items: end;
}

.event-row {
    background: #fff;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 15px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border-left: 4px solid #007bff;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.event-row:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
}

.event-row.planned { border-left-color: #007bff; }
.event-row.ongoing { border-left-color: #28a745; }
.event-row.completed { border-left-color: #6c757d; }
.event-row.cancelled { border-left-color: #dc3545; }

.event-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    margin-bottom: 15px;
    flex-wrap: wrap;
    gap: 15px;
}

.event-title {
    font-size: 1.3rem;
    font-weight: bold;
    color: #495057;
    margin: 0;
}

.event-badges {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.badge {
    padding: 5px 10px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: bold;
    text-transform: uppercase;
}

.badge-primary { background: #007bff; color: white; }
.badge-success { background: #28a745; color: white; }
.badge-secondary { background: #6c757d; color: white; }
.badge-danger { background: #dc3545; color: white; }
.badge-warning { background: #ffc107; color: #212529; }
.badge-info { background: #17a2b8; color: white; }

.event-meta {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
    margin-bottom: 15px;
    font-size: 0.9rem;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #6c757d;
}

.meta-item i {
    width: 16px;
    text-align: center;
}

.event-description {
    color: #495057;
    line-height: 1.5;
    margin-bottom: 15px;
    max-height: 60px;
    overflow: hidden;
    position: relative;
}

.event-description.expanded {
    max-height: none;
}

.event-actions {
    display: flex;
    gap: 8px;
    justify-content: end;
    flex-wrap: wrap;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 0.875rem;
    border-radius: 4px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-primary {
    background: #007bff;
    color: white;
}

.btn-primary:hover {
    background: #0056b3;
    color: white;
    text-decoration: none;
}

.btn-success {
    background: #28a745;
    color: white;
}

.btn-success:hover {
    background: #1e7e34;
    color: white;
    text-decoration: none;
}

.btn-warning {
    background: #ffc107;
    color: #212529;
}

.btn-warning:hover {
    background: #d39e00;
    color: #212529;
    text-decoration: none;
}

.btn-danger {
    background: #dc3545;
    color: white;
}

.btn-danger:hover {
    background: #bd2130;
    color: white;
    text-decoration: none;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #545b62;
    color: white;
    text-decoration: none;
}

.house-color-indicator {
    display: inline-block;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    margin-right: 6px;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
}

.empty-state i {
    font-size: 4rem;
    margin-bottom: 20px;
    opacity: 0.5;
}

@media (max-width: 768px) {
    .event-header {
        flex-direction: column;
        align-items: start;
    }
    
    .event-actions {
        justify-content: start;
        width: 100%;
    }
    
    .filter-row {
        grid-template-columns: 1fr;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>
@endpush

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>
                    <i class="fas fa-calendar-alt"></i>
                    {{ trans('dune-rp::admin.events.title') }}
                </h1>
            </div>
            <div class="col-sm-6">
                <div class="float-sm-right">
                    <a href="{{ route('dune-rp.admin.events.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        {{ trans('dune-rp::admin.events.create') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        {{-- Statistics Cards --}}
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">{{ number_format($stats['total']) }}</div>
                <div class="stat-label">{{ trans('dune-rp::admin.events.stats.total') }}</div>
            </div>
            <div class="stat-card planned">
                <div class="stat-number">{{ number_format($stats['planned']) }}</div>
                <div class="stat-label">{{ trans('dune-rp::admin.events.stats.planned') }}</div>
            </div>
            <div class="stat-card ongoing">
                <div class="stat-number">{{ number_format($stats['ongoing']) }}</div>
                <div class="stat-label">{{ trans('dune-rp::admin.events.stats.ongoing') }}</div>
            </div>
            <div class="stat-card completed">
                <div class="stat-number">{{ number_format($stats['completed']) }}</div>
                <div class="stat-label">{{ trans('dune-rp::admin.events.stats.completed') }}</div>
            </div>
            <div class="stat-card cancelled">
                <div class="stat-number">{{ number_format($stats['cancelled']) }}</div>
                <div class="stat-label">{{ trans('dune-rp::admin.events.stats.cancelled') }}</div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="filters-card">
            <h5 style="margin-bottom: 20px;">
                <i class="fas fa-filter"></i>
                {{ trans('dune-rp::admin.filters') }}
            </h5>
            
            <form method="GET" action="{{ route('dune-rp.admin.events.index') }}">
                <div class="filter-row">
                    <div class="form-group">
                        <label for="search">{{ trans('dune-rp::admin.search') }}</label>
                        <input type="text" id="search" name="search" class="form-control" 
                               value="{{ request('search') }}" 
                               placeholder="{{ trans('dune-rp::admin.events.search_placeholder') }}">
                    </div>
                    
                    <div class="form-group">
                        <label for="status">{{ trans('dune-rp::admin.events.status') }}</label>
                        <select id="status" name="status" class="form-control">
                            <option value="">{{ trans('dune-rp::admin.all_statuses') }}</option>
                            <option value="planned" {{ request('status') === 'planned' ? 'selected' : '' }}>
                                {{ trans('dune-rp::admin.events.status.planned') }}
                            </option>
                            <option value="ongoing" {{ request('status') === 'ongoing' ? 'selected' : '' }}>
                                {{ trans('dune-rp::admin.events.status.ongoing') }}
                            </option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>
                                {{ trans('dune-rp::admin.events.status.completed') }}
                            </option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>
                                {{ trans('dune-rp::admin.events.status.cancelled') }}
                            </option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="event_type">{{ trans('dune-rp::admin.events.type') }}</label>
                        <select id="event_type" name="event_type" class="form-control">
                            <option value="">{{ trans('dune-rp::admin.all_types') }}</option>
                            <option value="meeting" {{ request('event_type') === 'meeting' ? 'selected' : '' }}>
                                {{ trans('dune-rp::admin.events.types.meeting') }}
                            </option>
                            <option value="battle" {{ request('event_type') === 'battle' ? 'selected' : '' }}>
                                {{ trans('dune-rp::admin.events.types.battle') }}
                            </option>
                            <option value="ceremony" {{ request('event_type') === 'ceremony' ? 'selected' : '' }}>
                                {{ trans('dune-rp::admin.events.types.ceremony') }}
                            </option>
                            <option value="trade" {{ request('event_type') === 'trade' ? 'selected' : '' }}>
                                {{ trans('dune-rp::admin.events.types.trade') }}
                            </option>
                            <option value="exploration" {{ request('event_type') === 'exploration' ? 'selected' : '' }}>
                                {{ trans('dune-rp::admin.events.types.exploration') }}
                            </option>
                            <option value="other" {{ request('event_type') === 'other' ? 'selected' : '' }}>
                                {{ trans('dune-rp::admin.events.types.other') }}
                            </option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="organizer_house_id">{{ trans('dune-rp::admin.events.organizer_house') }}</label>
                        <select id="organizer_house_id" name="organizer_house_id" class="form-control">
                            <option value="">{{ trans('dune-rp::admin.all_houses') }}</option>
                            @foreach($houses as $house)
                                <option value="{{ $house->id }}" {{ request('organizer_house_id') == $house->id ? 'selected' : '' }}>
                                    {{ $house->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="date_from">{{ trans('dune-rp::admin.events.date_from') }}</label>
                        <input type="date" id="date_from" name="date_from" class="form-control" 
                               value="{{ request('date_from') }}">
                    </div>
                    
                    <div class="form-group">
                        <label for="date_to">{{ trans('dune-rp::admin.events.date_to') }}</label>
                        <input type="date" id="date_to" name="date_to" class="form-control" 
                               value="{{ request('date_to') }}">
                    </div>
                    
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <div style="display: flex; gap: 10px;">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                                {{ trans('dune-rp::admin.filter') }}
                            </button>
                            <a href="{{ route('dune-rp.admin.events.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i>
                                {{ trans('dune-rp::admin.clear') }}
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- Events List --}}
        @if($events->count() > 0)
            <div class="events-list">
                @foreach($events as $event)
                    <div class="event-row {{ $event->status }}">
                        <div class="event-header">
                            <div>
                                <h4 class="event-title">{{ $event->title }}</h4>
                                <div style="margin-top: 8px;">
                                    <small class="text-muted">
                                        {{ trans('dune-rp::admin.events.created_by') }} {{ $event->organizer->name }}
                                        {{ $event->created_at->diffForHumans() }}
                                    </small>
                                </div>
                            </div>
                            
                            <div class="event-badges">
                                @switch($event->status)
                                    @case('planned')
                                        <span class="badge badge-primary">{{ trans('dune-rp::admin.events.status.planned') }}</span>
                                        @break
                                    @case('ongoing')
                                        <span class="badge badge-success">{{ trans('dune-rp::admin.events.status.ongoing') }}</span>
                                        @break
                                    @case('completed')
                                        <span class="badge badge-secondary">{{ trans('dune-rp::admin.events.status.completed') }}</span>
                                        @break
                                    @case('cancelled')
                                        <span class="badge badge-danger">{{ trans('dune-rp::admin.events.status.cancelled') }}</span>
                                        @break
                                @endswitch
                                
                                @switch($event->event_type)
                                    @case('battle')
                                        <span class="badge badge-danger">{{ trans('dune-rp::admin.events.types.battle') }}</span>
                                        @break
                                    @case('ceremony')
                                        <span class="badge badge-warning">{{ trans('dune-rp::admin.events.types.ceremony') }}</span>
                                        @break
                                    @case('trade')
                                        <span class="badge badge-success">{{ trans('dune-rp::admin.events.types.trade') }}</span>
                                        @break
                                    @case('exploration')
                                        <span class="badge badge-info">{{ trans('dune-rp::admin.events.types.exploration') }}</span>
                                        @break
                                    @default
                                        <span class="badge badge-secondary">{{ trans('dune-rp::admin.events.types.' . $event->event_type) }}</span>
                                @endswitch
                            </div>
                        </div>
                        
                        <div class="event-meta">
                            <div class="meta-item">
                                <i class="fas fa-calendar"></i>
                                <span>{{ $event->event_date->format('d/m/Y H:i') }}</span>
                            </div>
                            
                            @if($event->location)
                                <div class="meta-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>{{ $event->location }}</span>
                                </div>
                            @endif
                            
                            @if($event->organizerHouse)
                                <div class="meta-item">
                                    <span class="house-color-indicator" style="background: {{ $event->organizerHouse->color }};"></span>
                                    <span>{{ $event->organizerHouse->name }}</span>
                                </div>
                            @endif
                            
                            <div class="meta-item">
                                <i class="fas fa-users"></i>
                                <span>{{ $event->participants()->count() }}{{ $event->max_participants ? '/' . $event->max_participants : '' }} {{ trans('dune-rp::admin.events.participants') }}</span>
                            </div>
                            
                            @if($event->spice_cost > 0 || $event->reward_spice > 0)
                                <div class="meta-item">
                                    <i class="fas fa-coins"></i>
                                    <span>
                                        @if($event->spice_cost > 0){{ number_format($event->spice_cost) }} coût@endif
                                        @if($event->spice_cost > 0 && $event->reward_spice > 0) • @endif
                                        @if($event->reward_spice > 0){{ number_format($event->reward_spice) }} récompense@endif
                                    </span>
                                </div>
                            @endif
                        </div>
                        
                        @if($event->description)
                            <div class="event-description">
                                {{ Str::limit($event->description, 200) }}
                                @if(strlen($event->description) > 200)
                                    <button type="button" class="btn btn-link btn-sm p-0" onclick="toggleDescription(this)">
                                        {{ trans('dune-rp::admin.show_more') }}
                                    </button>
                                    <div style="display: none;">{{ $event->description }}</div>
                                @endif
                            </div>
                        @endif
                        
                        <div class="event-actions">
                            <a href="{{ route('dune-rp.admin.events.show', $event) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-eye"></i>
                                {{ trans('dune-rp::admin.view') }}
                            </a>
                            
                            <a href="{{ route('dune-rp.admin.events.edit', $event) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i>
                                {{ trans('dune-rp::admin.edit') }}
                            </a>
                            
                            @if($event->status === 'planned')
                                <form action="{{ route('dune-rp.admin.events.complete', $event) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm" 
                                            onclick="return confirm('{{ trans('dune-rp::admin.events.confirm_complete') }}')">
                                        <i class="fas fa-check"></i>
                                        {{ trans('dune-rp::admin.events.complete') }}
                                    </button>
                                </form>
                                
                                <form action="{{ route('dune-rp.admin.events.cancel', $event) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-secondary btn-sm"
                                            onclick="return confirm('{{ trans('dune-rp::admin.events.confirm_cancel') }}')">
                                        <i class="fas fa-ban"></i>
                                        {{ trans('dune-rp::admin.events.cancel') }}
                                    </button>
                                </form>
                            @endif
                            
                            <form action="{{ route('dune-rp.admin.events.destroy', $event) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('{{ trans('dune-rp::admin.events.confirm_delete') }}')">
                                    <i class="fas fa-trash"></i>
                                    {{ trans('dune-rp::admin.delete') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
            
            {{-- Pagination --}}
            <div class="d-flex justify-content-center mt-4">
                {{ $events->appends(request()->query())->links() }}
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-calendar-times"></i>
                <h4>{{ trans('dune-rp::admin.events.no_events') }}</h4>
                <p>{{ trans('dune-rp::admin.events.no_events_desc') }}</p>
                <a href="{{ route('dune-rp.admin.events.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    {{ trans('dune-rp::admin.events.create_first') }}
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleDescription(button) {
    const description = button.parentElement;
    const shortText = description.firstChild.textContent;
    const fullDiv = description.querySelector('div');
    const fullText = fullDiv.textContent;
    
    if (description.classList.contains('expanded')) {
        description.classList.remove('expanded');
        description.firstChild.textContent = shortText;
        button.textContent = '{{ trans('dune-rp::admin.show_more') }}';
    } else {
        description.classList.add('expanded');
        description.firstChild.textContent = fullText;
        button.textContent = '{{ trans('dune-rp::admin.show_less') }}';
    }
}

// Auto-refresh for ongoing events
setInterval(function() {
    if (document.querySelector('.event-row.ongoing')) {
        const url = new URL(window.location);
        url.searchParams.set('auto_refresh', '1');
        
        fetch(url)
            .then(response => response.text())
            .then(html => {
                // Parse response and update only the events list
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newEventsList = doc.querySelector('.events-list');
                const currentEventsList = document.querySelector('.events-list');
                
                if (newEventsList && currentEventsList) {
                    currentEventsList.innerHTML = newEventsList.innerHTML;
                }
            })
            .catch(console.error);
    }
}, 60000); // Refresh every minute
</script>
@endpush
```
