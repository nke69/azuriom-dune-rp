```blade
@extends('admin.layouts.admin')

@section('title', trans('dune-rp::admin.houses.title'))

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
    text-align: center;
}

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

.house-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 25px;
}

.house-card {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    border-left: 6px solid #007bff;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.house-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.house-header {
    padding: 25px;
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border-bottom: 1px solid #dee2e6;
}

.house-name {
    font-size: 1.5rem;
    font-weight: bold;
    color: #495057;
    margin: 0 0 10px 0;
    display: flex;
    align-items: center;
    gap: 15px;
}

.house-logo {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    border: 3px solid currentColor;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    color: white;
}

.house-motto {
    font-style: italic;
    color: #6c757d;
    margin-bottom: 15px;
    font-size: 0.95rem;
}

.house-badges {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: bold;
    text-transform: uppercase;
}

.badge-success { background: #28a745; color: white; }
.badge-secondary { background: #6c757d; color: white; }
.badge-warning { background: #ffc107; color: #212529; }
.badge-danger { background: #dc3545; color: white; }

.house-body {
    padding: 25px;
}

.house-meta {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin-bottom: 20px;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.9rem;
}

.meta-item i {
    width: 16px;
    text-align: center;
    color: #6c757d;
}

.house-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    margin-bottom: 20px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
}

.stat-item {
    text-align: center;
}

.stat-value {
    font-size: 1.3rem;
    font-weight: bold;
    color: #495057;
}

.stat-desc {
    font-size: 0.8rem;
    color: #6c757d;
    text-transform: uppercase;
}

.house-description {
    color: #495057;
    line-height: 1.5;
    margin-bottom: 20px;
    max-height: 60px;
    overflow: hidden;
    position: relative;
}

.house-actions {
    display: flex;
    gap: 8px;
    justify-content: center;
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

.btn-warning {
    background: #ffc107;
    color: #212529;
}

.btn-danger {
    background: #dc3545;
    color: white;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.spice-indicator {
    display: flex;
    align-items: center;
    gap: 5px;
    font-weight: bold;
}

.spice-amount {
    color: #fd7e14;
    font-size: 1.1rem;
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

@media (max-width: 768px) {
    .house-grid {
        grid-template-columns: 1fr;
    }
    
    .house-meta {
        grid-template-columns: 1fr;
    }
    
    .house-stats {
        grid-template-columns: 1fr;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .filter-row {
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
                    <i class="fas fa-shield-alt"></i>
                    {{ trans('dune-rp::admin.houses.title') }}
                </h1>
            </div>
            <div class="col-sm-6">
                <div class="float-sm-right">
                    <a href="{{ route('dune-rp.admin.houses.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        {{ trans('dune-rp::admin.houses.create') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        {{-- Statistics --}}
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">{{ number_format($stats['total']) }}</div>
                <div class="stat-label">{{ trans('dune-rp::admin.houses.stats.total') }}</div>
            </div>
            <div class="stat-card" style="border-left-color: #28a745;">
                <div class="stat-number">{{ number_format($stats['active']) }}</div>
                <div class="stat-label">{{ trans('dune-rp::admin.houses.stats.active') }}</div>
            </div>
            <div class="stat-card" style="border-left-color: #fd7e14;">
                <div class="stat-number">{{ number_format($stats['total_members']) }}</div>
                <div class="stat-label">{{ trans('dune-rp::admin.houses.stats.total_members') }}</div>
            </div>
            <div class="stat-card" style="border-left-color: #dc3545;">
                <div class="stat-number">{{ number_format($stats['total_spice'], 0, ',', ' ') }}</div>
                <div class="stat-label">{{ trans('dune-rp::admin.houses.stats.total_spice') }}</div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="filters-card">
            <h5 style="margin-bottom: 20px;">
                <i class="fas fa-filter"></i>
                {{ trans('dune-rp::admin.filters') }}
            </h5>
            
            <form method="GET" action="{{ route('dune-rp.admin.houses.index') }}">
                <div class="filter-row">
                    <div class="form-group">
                        <label for="search">{{ trans('dune-rp::admin.search') }}</label>
                        <input type="text" id="search" name="search" class="form-control" 
                               value="{{ request('search') }}" 
                               placeholder="{{ trans('dune-rp::admin.houses.search_placeholder') }}">
                    </div>
                    
                    <div class="form-group">
                        <label for="status">{{ trans('dune-rp::admin.status') }}</label>
                        <select id="status" name="status" class="form-control">
                            <option value="">{{ trans('dune-rp::admin.all_statuses') }}</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>
                                {{ trans('dune-rp::admin.houses.active') }}
                            </option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>
                                {{ trans('dune-rp::admin.houses.inactive') }}
                            </option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="leader">{{ trans('dune-rp::admin.houses.has_leader') }}</label>
                        <select id="leader" name="leader" class="form-control">
                            <option value="">{{ trans('dune-rp::admin.all') }}</option>
                            <option value="yes" {{ request('leader') === 'yes' ? 'selected' : '' }}>
                                {{ trans('dune-rp::admin.houses.with_leader') }}
                            </option>
                            <option value="no" {{ request('leader') === 'no' ? 'selected' : '' }}>
                                {{ trans('dune-rp::admin.houses.without_leader') }}
                            </option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <div style="display: flex; gap: 10px;">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                                {{ trans('dune-rp::admin.filter') }}
                            </button>
                            <a href="{{ route('dune-rp.admin.houses.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i>
                                {{ trans('dune-rp::admin.clear') }}
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- Houses Grid --}}
        @if($houses->count() > 0)
            <div class="house-grid">
                @foreach($houses as $house)
                    <div class="house-card" style="border-left-color: {{ $house->color ?: '#007bff' }};">
                        <div class="house-header">
                            <h3 class="house-name" style="color: {{ $house->color ?: '#495057' }};">
                                @if($house->getImageUrl())
                                    <img src="{{ $house->getImageUrl() }}" alt="{{ $house->name }}" class="house-logo" style="border-color: {{ $house->color }};">
                                @else
                                    <div class="house-logo" style="background: {{ $house->color ?: '#007bff' }};">
                                        <i class="fas fa-shield-alt"></i>
                                    </div>
                                @endif
                                {{ $house->name }}
                            </h3>

                            @if($house->motto)
                                <div class="house-motto">"{{ $house->motto }}"</div>
                            @endif

                            <div class="house-badges">
                                @if($house->is_active)
                                    <span class="badge badge-success">{{ trans('dune-rp::admin.houses.active') }}</span>
                                @else
                                    <span class="badge badge-secondary">{{ trans('dune-rp::admin.houses.inactive') }}</span>
                                @endif
                                
                                @if($house->leader)
                                    <span class="badge badge-warning">{{ trans('dune-rp::admin.houses.has_leader') }}</span>
                                @else
                                    <span class="badge badge-danger">{{ trans('dune-rp::admin.houses.no_leader') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="house-body">
                            <div class="house-meta">
                                @if($house->leader)
                                    <div class="meta-item">
                                        <i class="fas fa-crown"></i>
                                        <span>{{ $house->leader->name }}</span>
                                    </div>
                                @endif
                                
                                @if($house->homeworld)
                                    <div class="meta-item">
                                        <i class="fas fa-globe"></i>
                                        <span>{{ $house->homeworld }}</span>
                                    </div>
                                @endif
                                
                                <div class="meta-item">
                                    <i class="fas fa-calendar"></i>
                                    <span>{{ $house->created_at->format('d/m/Y') }}</span>
                                </div>
                                
                                <div class="meta-item spice-indicator">
                                    <i class="fas fa-coins"></i>
                                    <span class="spice-amount">{{ number_format($house->spice_reserves) }}</span>
                                    <span>{{ trans('dune-rp::admin.spice.unit') }}</span>
                                </div>
                            </div>

                            <div class="house-stats">
                                <div class="stat-item">
                                    <div class="stat-value">{{ $house->characters()->count() }}</div>
                                    <div class="stat-desc">{{ trans('dune-rp::admin.houses.members') }}</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value">{{ $house->events()->count() }}</div>
                                    <div class="stat-desc">{{ trans('dune-rp::admin.houses.events') }}</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value">{{ number_format($house->influence_points) }}</div>
                                    <div class="stat-desc">{{ trans('dune-rp::admin.houses.influence') }}</div>
                                </div>
                            </div>

                            @if($house->description)
                                <div class="house-description">
                                    {{ Str::limit($house->description, 120) }}
                                </div>
                            @endif

                            <div class="house-actions">
                                <a href="{{ route('dune-rp.admin.houses.show', $house) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-eye"></i>
                                    {{ trans('dune-rp::admin.view') }}
                                </a>
                                
                                <a href="{{ route('dune-rp.admin.houses.edit', $house) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                    {{ trans('dune-rp::admin.edit') }}
                                </a>
                                
                                <button type="button" class="btn btn-success btn-sm" onclick="adjustSpice({{ $house->id }}, '{{ $house->name }}')">
                                    <i class="fas fa-coins"></i>
                                    {{ trans('dune-rp::admin.houses.adjust_spice') }}
                                </button>
                                
                                @if(!$house->is_active)
                                    <form action="{{ route('dune-rp.admin.houses.destroy', $house) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('{{ trans('dune-rp::admin.houses.confirm_delete') }}')">
                                            <i class="fas fa-trash"></i>
                                            {{ trans('dune-rp::admin.delete') }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-center mt-4">
                {{ $houses->appends(request()->query())->links() }}
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-shield-alt"></i>
                <h4>{{ trans('dune-rp::admin.houses.no_houses') }}</h4>
                <p>{{ trans('dune-rp::admin.houses.no_houses_desc') }}</p>
                <a href="{{ route('dune-rp.admin.houses.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    {{ trans('dune-rp::admin.houses.create_first') }}
                </a>
            </div>
        @endif
    </div>
</div>

{{-- Spice Adjustment Modal --}}
<div class="modal fade" id="spiceModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('dune-rp::admin.houses.adjust_spice') }}</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="spiceForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="adjustment_type">{{ trans('dune-rp::admin.houses.adjustment_type') }}</label>
                        <select id="adjustment_type" name="adjustment_type" class="form-control" required>
                            <option value="add">{{ trans('dune-rp::admin.houses.add_spice') }}</option>
                            <option value="remove">{{ trans('dune-rp::admin.houses.remove_spice') }}</option>
                            <option value="set">{{ trans('dune-rp::admin.houses.set_spice') }}</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="amount">{{ trans('dune-rp::admin.houses.amount') }}</label>
                        <input type="number" id="amount" name="amount" class="form-control" required min="0" step="0.01">
                    </div>
                    
                    <div class="form-group">
                        <label for="reason">{{ trans('dune-rp::admin.houses.reason') }}</label>
                        <input type="text" id="reason" name="reason" class="form-control" required maxlength="255"
                               placeholder="{{ trans('dune-rp::admin.houses.reason_placeholder') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        {{ trans('admin.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        {{ trans('dune-rp::admin.houses.adjust') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function adjustSpice(houseId, houseName) {
    const modal = document.getElementById('spiceModal');
    const form = document.getElementById('spiceForm');
    const title = modal.querySelector('.modal-title');
    
    title.textContent = `{{ trans('dune-rp::admin.houses.adjust_spice') }} - ${houseName}`;
    form.action = `/admin/dune-rp/houses/${houseId}/spice`;
    
    // Reset form
    form.reset();
    
    // Show modal (Bootstrap 4)
    $(modal).modal('show');
}

// Form validation
document.getElementById('spiceForm').addEventListener('submit', function(e) {
    const amount = document.getElementById('amount').value;
    const type = document.getElementById('adjustment_type').value;
    
    if (amount <= 0) {
        e.preventDefault();
        alert('Le montant doit être supérieur à 0');
        return false;
    }
    
    let confirmMessage = `Êtes-vous sûr de vouloir `;
    
    switch(type) {
        case 'add':
            confirmMessage += `ajouter ${amount} Épice`;
            break;
        case 'remove':
            confirmMessage += `retirer ${amount} Épice`;
            break;
        case 'set':
            confirmMessage += `définir les réserves à ${amount} Épice`;
            break;
    }
    
    confirmMessage += ' à cette maison ?';
    
    if (!confirm(confirmMessage)) {
        e.preventDefault();
    }
});

// Auto-refresh spice values (every 2 minutes)
setInterval(function() {
    const spiceElements = document.querySelectorAll('.spice-amount');
    if (spiceElements.length > 0) {
        // Simple refresh without full page reload
        fetch(window.location.href + '?ajax=spice')
            .then(response => response.json())
            .then(data => {
                if (data.houses) {
                    Object.keys(data.houses).forEach(houseId => {
                        const element = document.querySelector(`[data-house-id="${houseId}"] .spice-amount`);
                        if (element) {
                            element.textContent = new Intl.NumberFormat().format(data.houses[houseId]);
                        }
                    });
                }
            })
            .catch(console.error);
    }
}, 120000);
</script>
@endpush
```
