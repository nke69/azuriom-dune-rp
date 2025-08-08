@extends('admin.layouts.admin')

@section('title', trans('dune-rp::admin.characters.pending'))

@push('styles')
<style>
.character-card {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    background: #fff;
    transition: box-shadow 0.3s ease;
}

.character-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.character-header {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 15px;
}

.character-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #dee2e6;
}

.character-avatar-placeholder {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #dee2e6;
}

.character-info {
    flex: 1;
}

.character-name {
    font-size: 1.2rem;
    font-weight: bold;
    margin-bottom: 5px;
}

.character-meta {
    display: flex;
    gap: 15px;
    color: #6c757d;
    font-size: 0.9rem;
}

.character-bio {
    margin: 15px 0;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 4px;
    max-height: 150px;
    overflow-y: auto;
}

.character-actions {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #dee2e6;
}

.approval-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 5px;
}

.stat-label {
    color: #6c757d;
    font-size: 0.9rem;
}

.alert-info {
    border-left: 4px solid #17a2b8;
}
</style>
@endpush

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>
                    <i class="fas fa-user-clock"></i>
                    {{ trans('dune-rp::admin.characters.pending') }}
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.home') }}">{{ trans('admin.nav.dashboard') }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dune-rp.characters.index') }}">{{ trans('dune-rp::admin.characters.title') }}</a>
                    </li>
                    <li class="breadcrumb-item active">{{ trans('dune-rp::admin.characters.pending') }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        {{-- Statistics --}}
        <div class="approval-stats">
            <div class="stat-card">
                <div class="stat-number text-warning">{{ $characters->total() }}</div>
                <div class="stat-label">{{ trans('dune-rp::admin.characters.pending_approval') }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-number text-success">{{ \Azuriom\Plugin\DuneRp\Models\Character::where('is_approved', true)->whereDate('updated_at', today())->count() }}</div>
                <div class="stat-label">{{ trans('dune-rp::admin.characters.approved_today') }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-number text-info">{{ \Azuriom\Plugin\DuneRp\Models\Character::where('is_approved', true)->count() }}</div>
                <div class="stat-label">{{ trans('dune-rp::admin.characters.total_approved') }}</div>
            </div>
        </div>

        @if($characters->count() > 0)
            {{-- Bulk Actions --}}
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="selectAll">
                                <label class="custom-control-label" for="selectAll">
                                    {{ trans('dune-rp::admin.select_all') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6 text-right">
                            <button type="button" class="btn btn-success" id="bulkApprove" disabled>
                                <i class="fas fa-check"></i> {{ trans('dune-rp::admin.characters.bulk_approve') }}
                            </button>
                            <button type="button" class="btn btn-warning" id="bulkReject" disabled>
                                <i class="fas fa-times"></i> {{ trans('dune-rp::admin.characters.bulk_reject') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Character List --}}
            @foreach($characters as $character)
                <div class="character-card">
                    <div class="character-header">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input character-select" 
                                   id="character-{{ $character->id }}" value="{{ $character->id }}">
                            <label class="custom-control-label" for="character-{{ $character->id }}"></label>
                        </div>
                        
                        @if($character->avatar_url)
                            <img src="{{ $character->avatar_url }}" alt="{{ $character->name }}" class="character-avatar">
                        @else
                            <div class="character-avatar-placeholder">
                                <i class="fas fa-user"></i>
                            </div>
                        @endif
                        
                        <div class="character-info">
                            <div class="character-name">
                                {{ $character->name }}
                                @if($character->title)
                                    <small class="text-muted">- {{ $character->title }}</small>
                                @endif
                            </div>
                            <div class="character-meta">
                                <span><i class="fas fa-user"></i> {{ $character->user->name }}</span>
                                @if($character->house)
                                    <span><i class="fas fa-shield-alt"></i> {{ $character->house->name }}</span>
                                @endif
                                <span><i class="fas fa-clock"></i> {{ $character->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                    
                    @if($character->biography)
                        <div class="character-bio">
                            {{ Str::limit(strip_tags($character->biography), 300) }}
                        </div>
                    @endif
                    
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">
                                <strong>{{ trans('dune-rp::admin.characters.fields.birthworld') }}:</strong> {{ $character->birthworld ?? '-' }} |
                                <strong>{{ trans('dune-rp::admin.characters.fields.age') }}:</strong> {{ $character->age ?? '-' }} |
                                <strong>{{ trans('dune-rp::admin.characters.fields.status') }}:</strong> {{ $character->getStatusName() }}
                            </small>
                        </div>
                    </div>
                    
                    <div class="character-actions">
                        <a href="{{ route('admin.dune-rp.characters.show', $character) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i> {{ trans('dune-rp::admin.view') }}
                        </a>
                        <form action="{{ route('admin.dune-rp.characters.approve', $character) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="fas fa-check"></i> {{ trans('dune-rp::admin.characters.approve') }}
                            </button>
                        </form>
                        <form action="{{ route('admin.dune-rp.characters.reject', $character) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-sm">
                                <i class="fas fa-times"></i> {{ trans('dune-rp::admin.characters.reject') }}
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
            
            {{ $characters->links() }}
        @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                {{ trans('dune-rp::admin.characters.no_pending') }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.character-select');
    const bulkApprove = document.getElementById('bulkApprove');
    const bulkReject = document.getElementById('bulkReject');
    
    // Select all functionality
    selectAll.addEventListener('change', function() {
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkButtons();
    });
    
    // Individual checkbox change
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkButtons);
    });
    
    function updateBulkButtons() {
        const checkedCount = document.querySelectorAll('.character-select:checked').length;
        bulkApprove.disabled = checkedCount === 0;
        bulkReject.disabled = checkedCount === 0;
    }
    
    // Bulk approve
    bulkApprove.addEventListener('click', function() {
        if (confirm('{{ trans('dune-rp::admin.characters.confirm_bulk_approve') }}')) {
            const selectedIds = Array.from(document.querySelectorAll('.character-select:checked'))
                .map(cb => cb.value);
            // Créer un formulaire pour soumettre les IDs
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('admin.dune-rp.characters.bulk-approve') }}';
            form.innerHTML = '@csrf' + 
                selectedIds.map(id => `<input type="hidden" name="characters[]" value="${id}">`).join('');
            document.body.appendChild(form);
            form.submit();
        }
    });
    
    // Bulk reject
    bulkReject.addEventListener('click', function() {
        if (confirm('{{ trans('dune-rp::admin.characters.confirm_bulk_reject') }}')) {
            const selectedIds = Array.from(document.querySelectorAll('.character-select:checked'))
                .map(cb => cb.value);
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('admin.dune-rp.characters.bulk-reject') }}';
            form.innerHTML = '@csrf' + 
                selectedIds.map(id => `<input type="hidden" name="characters[]" value="${id}">`).join('');
            document.body.appendChild(form);
            form.submit();
        }
    });
});
</script>
@endpush
@endsection
