@extends('admin.layouts.admin')

@section('title', trans('dune-rp::admin.events.edit'))

@push('styles')
<style>
.form-card {
    background: #fff;
    border-radius: 8px;
    padding: 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 25px;
}

.form-section {
    border-bottom: 1px solid #e9ecef;
    padding-bottom: 25px;
    margin-bottom: 25px;
}

.form-section:last-child {
    border-bottom: none;
}

.section-title {
    color: #495057;
    font-size: 1.1rem;
    font-weight: bold;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.event-type-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 10px;
}

.event-type-option {
    padding: 10px;
    border: 2px solid #dee2e6;
    border-radius: 6px;
    cursor: pointer;
    text-align: center;
    transition: all 0.3s ease;
}

.event-type-option:hover {
    border-color: #007bff;
    background: #f0f8ff;
}

.event-type-option.selected {
    border-color: #007bff;
    background: #e3f2fd;
}

.status-badge {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 0.9rem;
}

.datetime-inputs {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 10px;
}

.spice-inputs {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}
</style>
@endpush

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>
                    <i class="fas fa-edit"></i>
                    {{ trans('dune-rp::admin.events.edit') }}: {{ $event->title }}
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.home') }}">{{ trans('admin.nav.dashboard') }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dune-rp.events.index') }}">{{ trans('dune-rp::admin.events.title') }}</a>
                    </li>
                    <li class="breadcrumb-item active">{{ trans('dune-rp::admin.events.edit') }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="form-card">
            @if($errors->any())
                <div class="alert alert-danger">
                    <h5><i class="fas fa-exclamation-triangle"></i> {{ trans('dune-rp::admin.errors.validation') }}</h5>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Current Status Display --}}
            <div class="alert alert-info mb-4">
                <strong>{{ trans('dune-rp::admin.events.current_status') }}:</strong>
                <span class="badge badge-{{ \Azuriom\Plugin\DuneRp\Models\RpEvent::STATUS_COLORS[$event->status] ?? 'secondary' }}">
                    {{ \Azuriom\Plugin\DuneRp\Models\RpEvent::STATUSES[$event->status] ?? $event->status }}
                </span>
                @if($event->status === 'cancelled' && $event->cancellation_reason)
                    <br><small>{{ trans('dune-rp::admin.events.cancellation_reason') }}: {{ $event->cancellation_reason }}</small>
                @endif
            </div>

            <form action="{{ route('admin.dune-rp.events.update', $event) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Basic Information --}}
                <div class="form-section">
                    <h4 class="section-title">
                        <i class="fas fa-info-circle"></i>
                        {{ trans('dune-rp::admin.events.basic_info') }}
                    </h4>

                    <div class="form-group">
                        <label for="title">{{ trans('dune-rp::admin.events.fields.title') }} *</label>
                        <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror" 
                               value="{{ old('title', $event->title) }}" required>
                        @error('title')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">{{ trans('dune-rp::admin.events.fields.description') }}</label>
                        <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" 
                                  rows="5">{{ old('description', $event->description) }}</textarea>
                        @error('description')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="event_type">{{ trans('dune-rp::admin.events.fields.event_type') }} *</label>
                        <select id="event_type" name="event_type" class="form-control @error('event_type') is-invalid @enderror" required>
                            @foreach(\Azuriom\Plugin\DuneRp\Models\RpEvent::EVENT_TYPES as $type => $label)
                                <option value="{{ $type }}" {{ old('event_type', $event->event_type) == $type ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('event_type')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Organization --}}
                <div class="form-section">
                    <h4 class="section-title">
                        <i class="fas fa-users"></i>
                        {{ trans('dune-rp::admin.events.organization') }}
                    </h4>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="organizer_id">{{ trans('dune-rp::admin.events.fields.organizer') }} *</label>
                                <select id="organizer_id" name="organizer_id" class="form-control @error('organizer_id') is-invalid @enderror" required>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('organizer_id', $event->organizer_id) == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('organizer_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="organizer_house_id">{{ trans('dune-rp::admin.events.fields.organizer_house') }}</label>
                                <select id="organizer_house_id" name="organizer_house_id" class="form-control @error('organizer_house_id') is-invalid @enderror">
                                    <option value="">{{ trans('dune-rp::admin.no_house') }}</option>
                                    @foreach($houses as $house)
                                        <option value="{{ $house->id }}" {{ old('organizer_house_id', $event->organizer_house_id) == $house->id ? 'selected' : '' }}>
                                            {{ $house->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('organizer_house_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Event Details --}}
                <div class="form-section">
                    <h4 class="section-title">
                        <i class="fas fa-calendar"></i>
                        {{ trans('dune-rp::admin.events.details') }}
                    </h4>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="event_date">{{ trans('dune-rp::admin.events.fields.event_date') }} *</label>
                                <input type="datetime-local" id="event_date" name="event_date" 
                                       class="form-control @error('event_date') is-invalid @enderror" 
                                       value="{{ old('event_date', $event->event_date->format('Y-m-d\TH:i')) }}" required>
                                @error('event_date')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="location">{{ trans('dune-rp::admin.events.fields.location') }}</label>
                                <input type="text" id="location" name="location" class="form-control @error('location') is-invalid @enderror" 
                                       value="{{ old('location', $event->location) }}">
                                @error('location')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="max_participants">{{ trans('dune-rp::admin.events.fields.max_participants') }}</label>
                        <input type="number" id="max_participants" name="max_participants" 
                               class="form-control @error('max_participants') is-invalid @enderror" 
                               value="{{ old('max_participants', $event->max_participants) }}" min="0">
                        <small class="text-muted">{{ trans('dune-rp::admin.events.max_participants_help') }}</small>
                        @error('max_participants')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Economics --}}
                <div class="form-section">
                    <h4 class="section-title">
                        <i class="fas fa-coins"></i>
                        {{ trans('dune-rp::admin.events.economics') }}
                    </h4>

                    <div class="spice-inputs">
                        <div class="form-group">
                            <label for="spice_cost">{{ trans('dune-rp::admin.events.fields.spice_cost') }}</label>
                            <input type="number" id="spice_cost" name="spice_cost" 
                                   class="form-control @error('spice_cost') is-invalid @enderror" 
                                   value="{{ old('spice_cost', $event->spice_cost) }}" min="0" step="0.01">
                            @error('spice_cost')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="reward_spice">{{ trans('dune-rp::admin.events.fields.reward_spice') }}</label>
                            <input type="number" id="reward_spice" name="reward_spice" 
                                   class="form-control @error('reward_spice') is-invalid @enderror" 
                                   value="{{ old('reward_spice', $event->reward_spice) }}" min="0" step="0.01">
                            @error('reward_spice')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Status --}}
                <div class="form-section">
                    <h4 class="section-title">
                        <i class="fas fa-toggle-on"></i>
                        {{ trans('dune-rp::admin.status') }}
                    </h4>

                    <div class="form-group">
                        <label for="status">{{ trans('dune-rp::admin.events.fields.status') }} *</label>
                        <select id="status" name="status" class="form-control @error('status') is-invalid @enderror" required>
                            @foreach(\Azuriom\Plugin\DuneRp\Models\RpEvent::STATUSES as $status => $label)
                                <option value="{{ $status }}" {{ old('status', $event->status) == $status ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_public" name="is_public" value="1" 
                                   {{ old('is_public', $event->is_public) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_public">
                                {{ trans('dune-rp::admin.events.fields.is_public') }}
                            </label>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> {{ trans('admin.save') }}
                    </button>
                    <a href="{{ route('admin.dune-rp.events.show', $event) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> {{ trans('admin.cancel') }}
                    </a>
                    
                    @if($event->status === 'planned')
                        <a href="#" class="btn btn-success" onclick="event.preventDefault(); document.getElementById('complete-form').submit();">
                            <i class="fas fa-check"></i> {{ trans('dune-rp::admin.events.complete') }}
                        </a>
                        <a href="#" class="btn btn-warning" onclick="event.preventDefault(); document.getElementById('cancel-form').submit();">
                            <i class="fas fa-ban"></i> {{ trans('dune-rp::admin.events.cancel') }}
                        </a>
                    @endif
                </div>
            </form>

            {{-- Hidden forms for quick actions --}}
            @if($event->status === 'planned')
                <form id="complete-form" action="{{ route('admin.dune-rp.events.complete', $event) }}" method="POST" style="display: none;">
                    @csrf
                </form>
                <form id="cancel-form" action="{{ route('admin.dune-rp.events.cancel', $event) }}" method="POST" style="display: none;">
                    @csrf
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
