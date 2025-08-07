```blade
@extends('admin.layouts.admin')

@section('title', $event->title . ' - ' . trans('dune-rp::admin.events.title'))

@push('styles')
<style>
.event-header {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border-radius: 8px;
    padding: 25px;
    margin-bottom: 25px;
    border-left: 4px solid {{ $event->organizerHouse ? $event->organizerHouse->color : '#007bff' }};
}

.event-title {
    color: #495057;
    font-size: 1.8rem;
    font-weight: bold;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.event-badges {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 15px;
}

.badge {
    padding: 6px 12px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: bold;
    text-transform: uppercase;
}

.badge-planned { background: #007bff; color: white; }
.badge-ongoing { background: #28a745; color: white; }
.badge-completed { background: #6c757d; color: white; }
.badge-cancelled { background: #dc3545; color: white; }

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
    margin-bottom: 30px;
}

.info-card {
    background: #fff;
    border-radius: 8px;
    padding: 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-left: 4px solid #007bff;
}

.info-card h5 {
    color: #495057;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #f1f3f4;
}

.info-item:last-child {
    border-bottom: none;
}

.info-label {
    color: #6c757d;
    font-weight: 500;
}

.info-value {
    color: #495057;
    font-weight: bold;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: #fff;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-left: 4px solid #007bff;
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    color: #495057;
    margin-bottom: 8px;
}

.stat-label {
    color: #6c757d;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.participants-section {
    background: #fff;
    border-radius: 8px;
    padding: 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 25px;
}

.participant-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px;
}

.participant-card {
    background: #f8f9fa;
    border-radius: 6px;
    padding: 15px;
    display: flex;
    align-items: center;
    gap: 12px;
    border-left: 3px solid #007bff;
}

.participant-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #007bff;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
}

.participant-info h6 {
    margin: 0;
    color: #495057;
}

.participant-info small {
    color: #6c757d;
}

.transactions-section {
    background: #fff;
    border-radius: 8px;
    padding: 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 25px;
}

.transaction-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #f1f3f4;
}

.transaction-item:last-child {
    border-bottom: none;
}

.transaction-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.transaction-amount {
    font-weight: bold;
}

.transaction-amount.positive {
    color: #28a745;
}

.transaction-amount.negative {
    color: #dc3545;
}

.action-buttons {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: 20px;
}

.house-indicator {
    display: inline-block;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    margin-right: 10px;
    vertical-align: middle;
}

@media (max-width: 768px) {
    .info-grid,
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .event-title {
        font-size: 1.4rem;
        flex-direction: column;
        align-items: flex-start;
    }
    
    .participant-list {
        grid-template-columns: 1fr;
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
                    {{ trans('dune-rp::admin.events.view') }}
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.home') }}">{{ trans('admin.nav.dashboard') }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('dune-rp.admin.events.index') }}">{{ trans('dune-rp::admin.events.title') }}</a>
                    </li>
                    <li class="breadcrumb-item active">{{ $event->title }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        {{-- Event Header --}}
        <div class="event-header">
            <div class="event-badges">
                @switch($event->status)
                    @case('planned')
                        <span class="badge badge-planned">{{ trans('dune-rp::admin.events.status.planned') }}</span>
                        @break
                    @case('ongoing')
                        <span class="badge badge-ongoing">{{ trans('dune-rp::admin.events.status.ongoing') }}</span>
                        @break
                    @case('completed')
                        <span class="badge badge-completed">{{ trans('dune-rp::admin.events.status.completed') }}</span>
                        @break
                    @case('cancelled')
                        <span class="badge badge-cancelled">{{ trans('dune-rp::admin.events.status.cancelled') }}</span>
                        @break
                @endswitch

                @switch($event->event_type)
                    @case('battle')
                        <span class="badge" style="background: #dc3545; color: white;">{{ trans('dune-rp::admin.events.types.battle') }}</span>
                        @break
                    @case('ceremony')
                        <span class="badge" style="background: #ffc107; color: #212529;">{{ trans('dune-rp::admin.events.types.ceremony') }}</span>
                        @break
                    @case('trade')
                        <span class="badge" style="background: #28a745; color: white;">{{ trans('dune-rp::admin.events.types.trade') }}</span>
                        @break
                    @case('exploration')
                        <span class="badge" style="background: #17a2b8; color: white;">{{ trans('dune-rp::admin.events.types.exploration') }}</span>
                        @break
                    @default
                        <span class="badge" style="background: #6c757d; color: white;">{{ trans('dune-rp::admin.events.types.' . $event->event_type) }}</span>
                @endswitch

                @if(!$event->is_public)
                    <span class="badge" style="background: #fd7e14; color: white;">{{ trans('dune-rp::admin.events.private') }}</span>
                @endif
            </div>

            <h2 class="event-title">
                @if($event->organizerHouse)
                    @if($event->organizerHouse->getImageUrl())
                        <img src="{{ $event->organizerHouse->getImageUrl() }}" alt="{{ $event->organizerHouse->name }}" 
                             style="width: 40px; height: 40px; border-radius: 50%; border: 2px solid {{ $event->organizerHouse->color }};">
                    @else
                        <div class="house-indicator" style="background: {{ $event->organizerHouse->color }};">
                            <i class="fas fa-shield-alt" style="color: white; font-size: 0.7rem;"></i>
                        </div>
                    @endif
                @endif
                {{ $event->title }}
            </h2>

            <div style="color: #6c757d;">
                <i class="fas fa-user"></i>
                {{ trans('dune-rp::admin.events.organized_by') }} <strong>{{ $event->organizer->name }}</strong>
                @if($event->organizerHouse)
                    • <strong style="color: {{ $event->organizerHouse->color }};">{{ $event->organizerHouse->name }}</strong>
                @endif
                • {{ $event->created_at->format('d/m/Y à H:i') }}
            </div>
        </div>

        {{-- Statistics --}}
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">{{ $event->participants()->count() }}</div>
                <div class="stat-label">{{ trans('dune-rp::admin.events.participants') }}</div>
            </div>
            
            @if($stats['total_cost'] > 0)
            <div class="stat-card" style="border-left-color: #dc3545;">
                <div class="stat-number" style="color: #dc3545;">{{ number_format($stats['total_cost']) }}</div>
                <div class="stat-label">{{ trans('dune-rp::admin.spice.total_cost') }}</div>
            </div>
            @endif
            
            @if($stats['total_reward'] > 0)
            <div class="stat-card" style="border-left-color: #28a745;">
                <div class="stat-number" style="color: #28a745;">{{ number_format($stats['total_reward']) }}</div>
                <div class="stat-label">{{ trans('dune-rp::admin.spice.total_reward') }}</div>
            </div>
            @endif
            
            <div class="stat-card" style="border-left-color: #17a2b8;">
                <div class="stat-number">{{ $stats['related_transactions'] }}</div>
                <div class="stat-label">{{ trans('dune-rp::admin.transactions') }}</div>
            </div>
            
            @if($stats['transaction_volume'] != 0)
            <div class="stat-card" style="border-left-color: #fd7e14;">
                <div class="stat-number" style="color: {{ $stats['transaction_volume'] > 0 ? '#28a745' : '#dc3545' }};">
                    {{ number_format(abs($stats['transaction_volume'])) }}
                </div>
                <div class="stat-label">{{ trans('dune-rp::admin.spice.volume') }}</div>
            </div>
            @endif
        </div>

        {{-- Event Information --}}
        <div class="info-grid">
            <div class="info-card">
                <h5>
                    <i class="fas fa-info-circle"></i>
                    {{ trans('dune-rp::admin.events.event_details') }}
                </h5>

                <div class="info-item">
                    <span class="info-label">{{ trans('dune-rp::admin.events.fields.event_date') }}</span>
                    <span class="info-value">
                        {{ $event->event_date->format('d/m/Y à H:i') }}
                        <small class="text-muted">({{ $event->event_date->diffForHumans() }})</small>
                    </span>
                </div>

                @if($event->location)
                <div class="info-item">
                    <span class="info-label">{{ trans('dune-rp::admin.events.fields.location') }}</span>
                    <span class="info-value">{{ $event->location }}</span>
                </div>
                @endif

                <div class="info-item">
                    <span class="info-label">{{ trans('dune-rp::admin.events.fields.max_participants') }}</span>
                    <span class="info-value">
                        {{ $event->max_participants ?: trans('dune-rp::admin.events.unlimited') }}
                    </span>
                </div>

                <div class="info-item">
                    <span class="info-label">{{ trans('dune-rp::admin.events.visibility') }}</span>
                    <span class="info-value">
                        @if($event->is_public)
                            <i class="fas fa-globe text-success"></i> {{ trans('dune-rp::admin.events.public') }}
                        @else
                            <i class="fas fa-lock text-warning"></i> {{ trans('dune-rp::admin.events.private') }}
                        @endif
                    </span>
                </div>

                <div class="info-item">
                    <span class="info-label">{{ trans('dune-rp::admin.events.created') }}</span>
                    <span class="info-value">{{ $event->created_at->format('d/m/Y à H:i') }}</span>
                </div>

                @if($event->updated_at != $event->created_at)
                <div class="info-item">
                    <span class="info-label">{{ trans('dune-rp::admin.events.updated') }}</span>
                    <span class="info-value">{{ $event->updated_at->format('d/m/Y à H:i') }}</span>
                </div>
                @endif
            </div>

            <div class="info-card">
                <h5>
                    <i class="fas fa-coins"></i>
                    {{ trans('dune-rp::admin.events.spice_economics') }}
                </h5>

                <div class="info-item">
                    <span class="info-label">{{ trans('dune-rp::admin.events.fields.spice_cost') }}</span>
                    <span class="info-value">
                        @if($event->spice_cost > 0)
                            <span style="color: #dc3545;">{{ number_format($event->spice_cost) }} {{ trans('dune-rp::admin.spice.unit') }}</span>
                        @else
                            <span class="text-muted">{{ trans('dune-rp::admin.events.free') }}</span>
                        @endif
                    </span>
                </div>

                <div class="info-item">
                    <span class="info-label">{{ trans('dune-rp::admin.events.fields.reward_spice') }}</span>
                    <span class="info-value">
                        @if($event->reward_spice > 0)
                            <span style="color: #28a745;">{{ number_format($event->reward_spice) }} {{ trans('dune-rp::admin.spice.unit') }}</span>
                        @else
                            <span class="text-muted">{{ trans('dune-rp::admin.events.no_reward') }}</span>
                        @endif
                    </span>
                </div>

                @if($event->participants()->count() > 0 && $event->spice_cost > 0)
                <div class="info-item">
                    <span class="info-label">{{ trans('dune-rp::admin.spice.total_collected') }}</span>
                    <span class="info-value">
                        <span style="color: #dc3545;">{{ number_format($event->spice_cost * $event->participants()->count()) }} {{ trans('dune-rp::admin.spice.unit') }}</span>
                    </span>
                </div>
                @endif

                @if($event->participants()->count() > 0 && $event->reward_spice > 0)
                <div class="info-item">
                    <span class="info-label">{{ trans('dune-rp::admin.spice.total_to_distribute') }}</span>
                    <span class="info-value">
                        <span style="color: #28a745;">{{ number_format($event->reward_spice * $event->participants()->count()) }} {{ trans('dune-rp::admin.spice.unit') }}</span>
                    </span>
                </div>
                @endif
            </div>
        </div>

        {{-- Event Description --}}
        @if($event->description)
        <div class="info-card">
            <h5>
                <i class="fas fa-align-left"></i>
                {{ trans('dune-rp::admin.events.description') }}
            </h5>
            <div style="line-height: 1.6; color: #495057;">
                {!! nl2br(e($event->description)) !!}
            </div>
        </div>
        @endif

        {{-- Participants --}}
        @if($event->participants()->count() > 0)
        <div class="participants-section">
            <h5 style="margin-bottom: 20px;">
                <i class="fas fa-users"></i>
                {{ trans('dune-rp::admin.events.participants') }} ({{ $event->participants()->count() }})
            </h5>

            <div class="participant-list">
                @foreach($event->participants as $participant)
                <div class="participant-card" style="border-left-color: {{ $participant->house ? $participant->house->color : '#007bff' }};">
                    <div class="participant-avatar" style="background: {{ $participant->house ? $participant->house->color : '#007bff' }};">
                        {{ strtoupper(substr($participant->user->name, 0, 1)) }}
                    </div>
                    <div class="participant-info">
                        <h6>{{ $participant->user->name }}</h6>
                        <small>
                            @if($participant->house)
                                {{ $participant->house->name }} • 
                            @endif
                            {{ trans('dune-rp::admin.events.joined') }} {{ $participant->created_at->diffForHumans() }}
                        </small>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Related Transactions --}}
        @if($event->spiceTransactions->count() > 0)
        <div class="transactions-section">
            <h5 style="margin-bottom: 20px;">
                <i class="fas fa-exchange-alt"></i>
                {{ trans('dune-rp::admin.events.related_transactions') }} ({{ $event->spiceTransactions->count() }})
            </h5>

            @foreach($event->spiceTransactions as $transaction)
            <div class="transaction-item">
                <div class="transaction-info">
                    <i class="fas {{ $transaction->type === 'income' ? 'fa-plus-circle text-success' : 'fa-minus-circle text-danger' }}"></i>
                    <div>
                        <div>{{ $transaction->description }}</div>
                        <small class="text-muted">
                            {{ $transaction->created_at->format('d/m/Y à H:i') }}
                            @if($transaction->user)
                                • {{ $transaction->user->name }}
                            @endif
                        </small>
                    </div>
                </div>
                <div class="transaction-amount {{ $transaction->type === 'income' ? 'positive' : 'negative' }}">
                    {{ $transaction->type === 'income' ? '+' : '-' }}{{ number_format(abs($transaction->amount)) }} {{ trans('dune-rp::admin.spice.unit') }}
                </div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Action Buttons --}}
        <div class="action-buttons">
            <a href="{{ route('dune-rp.admin.events.edit', $event) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i>
                {{ trans('admin.edit') }}
            </a>

            <a href="{{ route('dune-rp.events.show', $event) }}" class="btn btn-info" target="_blank">
                <i class="fas fa-external-link-alt"></i>
                {{ trans('dune-rp::admin.events.view_public') }}
            </a>

            @if($event->status === 'planned')
                <form action="{{ route('dune-rp.admin.events.complete', $event) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-success" 
                            onclick="return confirm('{{ trans('dune-rp::admin.events.confirm_complete') }}')">
                        <i class="fas fa-check"></i>
                        {{ trans('dune-rp::admin.events.complete') }}
                    </button>
                </form>

                <form action="{{ route('dune-rp.admin.events.cancel', $event) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-warning"
                            onclick="return confirm('{{ trans('dune-rp::admin.events.confirm_cancel') }}')">
                        <i class="fas fa-ban"></i>
                        {{ trans('dune-rp::admin.events.cancel') }}
                    </button>
                </form>
            @endif

            <form action="{{ route('dune-rp.admin.events.destroy', $event) }}" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger"
                        onclick="return confirm('{{ trans('dune-rp::admin.events.confirm_delete') }}')">
                    <i class="fas fa-trash"></i>
                    {{ trans('admin.delete') }}
                </button>
            </form>

            <a href="{{ route('dune-rp.admin.events.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                {{ trans('admin.back') }}
            </a>
        </div>
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

// Confirmation dialogs with more context
document.querySelectorAll('form[action*="complete"]').forEach(form => {
    form.addEventListener('submit', function(e) {
        const participantCount = {{ $event->participants()->count() }};
        let message = 'Êtes-vous sûr de vouloir marquer cet événement comme terminé ?';
        
        if (participantCount > 0) {
            message += `\n\nCela affectera ${participantCount} participant(s).`;
            
            @if($event->reward_spice > 0)
            const totalReward = {{ $event->reward_spice }} * participantCount;
            message += `\nLes récompenses totales à distribuer : ${totalReward} Épice`;
            @endif
        }
        
        if (!confirm(message)) {
            e.preventDefault();
        }
    });
});

document.querySelectorAll('form[action*="destroy"]').forEach(form => {
    form.addEventListener('submit', function(e) {
        const participantCount = {{ $event->participants()->count() }};
        const transactionCount = {{ $event->spiceTransactions->count() }};
        
        let message = 'Êtes-vous sûr de vouloir supprimer cet événement ?\n\nCette action est irréversible.';
        
        if (participantCount > 0) {
            message += `\n\n⚠️ ${participantCount} participant(s) sont inscrits à cet événement.`;
        }
        
        if (transactionCount > 0) {
            message += `\n⚠️ ${transactionCount} transaction(s) sont liées à cet événement.`;
        }
        
        if (!confirm(message)) {
            e.preventDefault();
        }
    });
});
</script>
@endpush
```
