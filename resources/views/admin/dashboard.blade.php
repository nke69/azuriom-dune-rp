@extends('admin.layouts.admin')

@section('title', trans('dune-rp::admin.dashboard.title'))

@push('footer-scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>
                    <i class="fas fa-tachometer-alt"></i>
                    {{ trans('dune-rp::admin.dashboard.title') }}
                </h1>
            </div>
            <div class="col-sm-6">
                <div class="float-sm-right">
                    <a href="{{ route('admin.dune-rp.export-stats') }}" class="btn btn-sm btn-info">
                        <i class="fas fa-download"></i> {{ trans('dune-rp::admin.dashboard.export') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        {{-- Alertes --}}
        @if(count($alerts) > 0)
            @foreach($alerts as $alert)
                <div class="alert alert-{{ $alert['type'] }} alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="fas fa-{{ $alert['type'] === 'warning' ? 'exclamation-triangle' : 'info-circle' }}"></i>
                    {{ $alert['message'] }}
                    @if(isset($alert['action']))
                        <a href="{{ $alert['action'] }}" class="alert-link ml-2">
                            {{ $alert['action_text'] }} →
                        </a>
                    @endif
                </div>
            @endforeach
        @endif

        {{-- Statistiques principales --}}
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $stats['houses']['total'] }}</h3>
                        <p>{{ trans('dune-rp::admin.dashboard.total_houses') }}</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <a href="{{ route('admin.dune-rp.houses.index') }}" class="small-box-footer">
                        {{ trans('dune-rp::admin.dashboard.view_all') }} <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $stats['characters']['approved'] }}</h3>
                        <p>{{ trans('dune-rp::admin.dashboard.approved_characters') }}</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <a href="{{ route('admin.dune-rp.characters.index') }}" class="small-box-footer">
                        {{ trans('dune-rp::admin.dashboard.view_all') }} <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $stats['characters']['pending'] }}</h3>
                        <p>{{ trans('dune-rp::admin.dashboard.pending_characters') }}</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-clock"></i>
                    </div>
                    <a href="{{ route('admin.dune-rp.characters.pending') }}" class="small-box-footer">
                        {{ trans('dune-rp::admin.dashboard.review') }} <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ number_format($stats['spice']['total_reserves'], 0) }}</h3>
                        <p>{{ trans('dune-rp::admin.dashboard.total_spice') }}</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-coins"></i>
                    </div>
                    <a href="{{ route('admin.dune-rp.houses.index') }}" class="small-box-footer">
                        {{ trans('dune-rp::admin.dashboard.manage') }} <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Graphique d'activité --}}
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line"></i>
                            {{ trans('dune-rp::admin.dashboard.activity_chart') }}
                        </h3>
                    </div>
                    <div class="card-body">
                        <canvas id="activityChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>

            {{-- Top Maisons --}}
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-trophy"></i>
                            {{ trans('dune-rp::admin.dashboard.top_houses') }}
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @foreach($topHouses as $index => $house)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge badge-primary mr-2">{{ $index + 1 }}</span>
                                        <strong>{{ $house->name }}</strong>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-muted small">{{ number_format($house->influence_points) }} pts</div>
                                        <div class="text-warning small">{{ number_format($house->spice_reserves, 0) }} épice</div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Personnages en attente --}}
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-clock"></i>
                            {{ trans('dune-rp::admin.dashboard.pending_approvals') }}
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.dune-rp.characters.pending') }}" class="btn btn-sm btn-primary">
                                {{ trans('dune-rp::admin.dashboard.view_all') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if($pendingCharacters->count() > 0)
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>{{ trans('dune-rp::admin.dashboard.character') }}</th>
                                        <th>{{ trans('dune-rp::admin.dashboard.player') }}</th>
                                        <th>{{ trans('dune-rp::admin.dashboard.house') }}</th>
                                        <th>{{ trans('dune-rp::admin.dashboard.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingCharacters as $character)
                                        <tr>
                                            <td>{{ $character->name }}</td>
                                            <td>{{ $character->user->name }}</td>
                                            <td>{{ $character->house->name ?? '-' }}</td>
                                            <td>
                                                <a href="{{ route('admin.dune-rp.characters.show', $character) }}" 
                                                   class="btn btn-xs btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="p-3 text-center text-muted">
                                {{ trans('dune-rp::admin.dashboard.no_pending') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Événements à venir --}}
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-calendar-alt"></i>
                            {{ trans('dune-rp::admin.dashboard.upcoming_events') }}
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.dune-rp.events.create') }}" class="btn btn-sm btn-success">
                                <i class="fas fa-plus"></i> {{ trans('dune-rp::admin.dashboard.create') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if($upcomingEvents->count() > 0)
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>{{ trans('dune-rp::admin.dashboard.event') }}</th>
                                        <th>{{ trans('dune-rp::admin.dashboard.date') }}</th>
                                        <th>{{ trans('dune-rp::admin.dashboard.type') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upcomingEvents as $event)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.dune-rp.events.show', $event) }}">
                                                    {{ $event->title }}
                                                </a>
                                            </td>
                                            <td>{{ $event->event_date->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <span class="badge badge-{{ \Azuriom\Plugin\DuneRp\Models\RpEvent::TYPE_COLORS[$event->event_type] ?? 'secondary' }}">
                                                    {{ $event->getEventTypeName() }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="p-3 text-center text-muted">
                                {{ trans('dune-rp::admin.dashboard.no_events') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Transactions récentes --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-exchange-alt"></i>
                            {{ trans('dune-rp::admin.dashboard.recent_transactions') }}
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        @if($recentTransactions->count() > 0)
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>{{ trans('dune-rp::admin.dashboard.date') }}</th>
                                        <th>{{ trans('dune-rp::admin.dashboard.house') }}</th>
                                        <th>{{ trans('dune-rp::admin.dashboard.type') }}</th>
                                        <th>{{ trans('dune-rp::admin.dashboard.amount') }}</th>
                                        <th>{{ trans('dune-rp::admin.dashboard.reason') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentTransactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->created_at->format('d/m H:i') }}</td>
                                            <td>{{ $transaction->house->name }}</td>
                                            <td>
                                                <span class="badge badge-{{ $transaction->getTypeColor() }}">
                                                    {{ $transaction->getTypeName() }}
                                                </span>
                                            </td>
                                            <td class="{{ $transaction->isPositive() ? 'text-success' : 'text-danger' }}">
                                                {{ $transaction->isPositive() ? '+' : '-' }}{{ number_format($transaction->amount, 0) }}
                                            </td>
                                            <td>{{ Str::limit($transaction->reason, 30) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="p-3 text-center text-muted">
                                {{ trans('dune-rp::admin.dashboard.no_transactions') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('footer-scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Graphique d'activité
    const ctx = document.getElementById('activityChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($creationStats['dates']),
            datasets: [{
                label: '{{ trans('dune-rp::admin.dashboard.houses') }}',
                data: @json($creationStats['houses']),
                borderColor: 'rgb(54, 162, 235)',
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                tension: 0.1
            }, {
                label: '{{ trans('dune-rp::admin.dashboard.characters') }}',
                data: @json($creationStats['characters']),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.1
            }, {
                label: '{{ trans('dune-rp::admin.dashboard.events') }}',
                data: @json($creationStats['events']),
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>
@endpush
@endsection
