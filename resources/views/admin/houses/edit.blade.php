@extends('admin.layouts.admin')

@section('title', trans('dune-rp::admin.houses.edit'))

@push('styles')
<style>
.form-card {
    background: #fff;
    border-radius: 8px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 25px;
}

.form-section {
    border-bottom: 1px solid #e9ecef;
    padding-bottom: 30px;
    margin-bottom: 30px;
}

.form-section:last-child {
    border-bottom: none;
    padding-bottom: 0;
    margin-bottom: 0;
}

.section-title {
    color: #495057;
    font-size: 1.2rem;
    font-weight: bold;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.color-preview {
    display: flex;
    align-items: center;
    gap: 10px;
}

.color-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 3px solid #dee2e6;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    color: white;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
}

.current-sigil {
    max-width: 150px;
    border-radius: 8px;
    border: 2px solid #dee2e6;
    margin-bottom: 10px;
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
                    {{ trans('dune-rp::admin.houses.edit') }}: {{ $house->name }}
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.home') }}">{{ trans('admin.nav.dashboard') }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dune-rp.houses.index') }}">{{ trans('dune-rp::admin.houses.title') }}</a>
                    </li>
                    <li class="breadcrumb-item active">{{ trans('dune-rp::admin.houses.edit') }}</li>
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

            <form action="{{ route('admin.dune-rp.houses.update', $house) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Basic Information --}}
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-info-circle"></i>
                        {{ trans('dune-rp::admin.houses.basic_info') }}
                    </h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">{{ trans('dune-rp::admin.houses.fields.name') }} *</label>
                            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $house->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="motto">{{ trans('dune-rp::admin.houses.fields.motto') }}</label>
                            <input type="text" id="motto" name="motto" class="form-control @error('motto') is-invalid @enderror" 
                                   value="{{ old('motto', $house->motto) }}">
                            @error('motto')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">{{ trans('dune-rp::admin.houses.fields.description') }}</label>
                        <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" 
                                  rows="5">{{ old('description', $house->description) }}</textarea>
                        @error('description')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Leadership --}}
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-crown"></i>
                        {{ trans('dune-rp::admin.houses.leadership') }}
                    </h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="leader_id">{{ trans('dune-rp::admin.houses.fields.leader') }}</label>
                            <select id="leader_id" name="leader_id" class="form-control @error('leader_id') is-invalid @enderror">
                                <option value="">{{ trans('dune-rp::admin.no_leader') }}</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('leader_id', $house->leader_id) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('leader_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="homeworld">{{ trans('dune-rp::admin.houses.fields.homeworld') }}</label>
                            <input type="text" id="homeworld" name="homeworld" class="form-control @error('homeworld') is-invalid @enderror" 
                                   value="{{ old('homeworld', $house->homeworld) }}">
                            @error('homeworld')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Resources --}}
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-coins"></i>
                        {{ trans('dune-rp::admin.houses.resources') }}
                    </h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="spice_reserves">{{ trans('dune-rp::admin.houses.fields.spice_reserves') }}</label>
                            <input type="number" id="spice_reserves" name="spice_reserves" class="form-control @error('spice_reserves') is-invalid @enderror" 
                                   value="{{ old('spice_reserves', $house->spice_reserves) }}" min="0" step="0.01">
                            @error('spice_reserves')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="influence_points">{{ trans('dune-rp::admin.houses.fields.influence_points') }}</label>
                            <input type="number" id="influence_points" name="influence_points" class="form-control @error('influence_points') is-invalid @enderror" 
                                   value="{{ old('influence_points', $house->influence_points) }}" min="0">
                            @error('influence_points')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Visual Identity --}}
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-palette"></i>
                        {{ trans('dune-rp::admin.houses.visual_identity') }}
                    </h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="color">{{ trans('dune-rp::admin.houses.fields.color') }}</label>
                            <div class="color-preview">
                                <input type="color" id="color" name="color" class="form-control @error('color') is-invalid @enderror" 
                                       value="{{ old('color', $house->color) }}" style="width: 60px;">
                                <div class="color-circle" style="background: {{ old('color', $house->color) }};">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                            </div>
                            @error('color')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="sigil">{{ trans('dune-rp::admin.houses.fields.sigil') }}</label>
                            @if($house->sigil_url)
                                <div>
                                    <img src="{{ $house->sigil_url }}" alt="{{ $house->name }}" class="current-sigil">
                                    <p class="text-muted small">{{ trans('dune-rp::admin.houses.current_sigil') }}</p>
                                </div>
                            @endif
                            <input type="file" id="sigil" name="sigil" class="form-control-file @error('sigil') is-invalid @enderror" accept="image/*">
                            @error('sigil')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Status --}}
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-toggle-on"></i>
                        {{ trans('dune-rp::admin.status') }}
                    </h3>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" 
                                   {{ old('is_active', $house->is_active) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">
                                {{ trans('dune-rp::admin.houses.fields.is_active') }}
                            </label>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> {{ trans('admin.save') }}
                    </button>
                    <a href="{{ route('admin.dune-rp.houses.show', $house) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> {{ trans('admin.cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Live color preview
    const colorInput = document.getElementById('color');
    const colorCircle = document.querySelector('.color-circle');
    
    colorInput.addEventListener('input', function() {
        colorCircle.style.backgroundColor = this.value;
    });
});
</script>
@endpush
@endsection
