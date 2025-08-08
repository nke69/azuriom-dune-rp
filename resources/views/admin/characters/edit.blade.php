@extends('admin.layouts.admin')

@section('title', trans('dune-rp::admin.characters.edit'))

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>
                    <i class="fas fa-edit"></i>
                    {{ trans('dune-rp::admin.characters.edit') }}: {{ $character->name }}
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
                    <li class="breadcrumb-item active">{{ trans('dune-rp::admin.characters.edit') }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <form action="{{ route('admin.dune-rp.characters.update', $character) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ trans('dune-rp::admin.characters.character_info') }}</h3>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">{{ trans('dune-rp::admin.characters.fields.name') }} *</label>
                                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $character->name) }}" required>
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="title">{{ trans('dune-rp::admin.characters.fields.title') }}</label>
                                <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror" 
                                       value="{{ old('title', $character->title) }}">
                                @error('title')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="house_id">{{ trans('dune-rp::admin.characters.fields.house') }}</label>
                                <select id="house_id" name="house_id" class="form-control @error('house_id') is-invalid @enderror">
                                    <option value="">{{ trans('dune-rp::admin.no_house') }}</option>
                                    @foreach($houses as $house)
                                        <option value="{{ $house->id }}" {{ old('house_id', $character->house_id) == $house->id ? 'selected' : '' }}>
                                            {{ $house->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('house_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ trans('dune-rp::admin.characters.fields.player') }}</label>
                                <input type="text" class="form-control" value="{{ $character->user->name }}" disabled>
                                <small class="text-muted">{{ trans('dune-rp::admin.characters.player_cannot_change') }}</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="biography">{{ trans('dune-rp::admin.characters.fields.biography') }}</label>
                        <textarea id="biography" name="biography" class="form-control @error('biography') is-invalid @enderror" 
                                  rows="5">{{ old('biography', $character->biography) }}</textarea>
                        @error('biography')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="birthworld">{{ trans('dune-rp::admin.characters.fields.birthworld') }}</label>
                                <input type="text" id="birthworld" name="birthworld" class="form-control @error('birthworld') is-invalid @enderror" 
                                       value="{{ old('birthworld', $character->birthworld) }}">
                                @error('birthworld')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="age">{{ trans('dune-rp::admin.characters.fields.age') }}</label>
                                <input type="number" id="age" name="age" class="form-control @error('age') is-invalid @enderror" 
                                       value="{{ old('age', $character->age) }}" min="1" max="500">
                                @error('age')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status">{{ trans('dune-rp::admin.characters.fields.status') }} *</label>
                                <select id="status" name="status" class="form-control @error('status') is-invalid @enderror" required>
                                    @foreach(\Azuriom\Plugin\DuneRp\Models\Character::STATUSES as $key => $label)
                                        <option value="{{ $key }}" {{ old('status', $character->status) == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="spice_addiction_level">{{ trans('dune-rp::admin.characters.fields.spice_addiction') }}</label>
                        <select id="spice_addiction_level" name="spice_addiction_level" class="form-control @error('spice_addiction_level') is-invalid @enderror" required>
                            @foreach(\Azuriom\Plugin\DuneRp\Models\Character::ADDICTION_LEVELS as $level => $label)
                                <option value="{{ $level }}" {{ old('spice_addiction_level', $character->spice_addiction_level) == $level ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('spice_addiction_level')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="special_abilities">{{ trans('dune-rp::admin.characters.fields.abilities') }}</label>
                        <select id="special_abilities" name="special_abilities[]" class="form-control @error('special_abilities') is-invalid @enderror" multiple>
                            @foreach(\Azuriom\Plugin\DuneRp\Models\Character::SPECIAL_ABILITIES as $key => $ability)
                                <option value="{{ $key }}" {{ in_array($key, old('special_abilities', $character->special_abilities ?? [])) ? 'selected' : '' }}>
                                    {{ $ability }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">{{ trans('dune-rp::admin.characters.abilities_help') }}</small>
                        @error('special_abilities')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="avatar">{{ trans('dune-rp::admin.characters.fields.avatar') }}</label>
                        @if($character->avatar_url)
                            <div class="mb-2">
                                <img src="{{ $character->avatar_url }}" alt="{{ $character->name }}" style="max-width: 150px; border-radius: 8px;">
                                <p class="text-muted small">{{ trans('dune-rp::admin.characters.current_avatar') }}</p>
                            </div>
                        @endif
                        <input type="file" id="avatar" name="avatar" class="form-control-file @error('avatar') is-invalid @enderror" accept="image/*">
                        @error('avatar')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="is_public" name="is_public" value="1" 
                                           {{ old('is_public', $character->is_public) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_public">
                                        {{ trans('dune-rp::admin.characters.fields.is_public') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="is_approved" name="is_approved" value="1" 
                                           {{ old('is_approved', $character->is_approved) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_approved">
                                        {{ trans('dune-rp::admin.characters.fields.is_approved') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> {{ trans('admin.save') }}
                    </button>
                    <a href="{{ route('admin.dune-rp.characters.show', $character) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> {{ trans('admin.cancel') }}
                    </a>
                    <form action="{{ route('admin.dune-rp.characters.destroy', $character) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('{{ trans('dune-rp::admin.characters.delete_confirm') }}')">
                            <i class="fas fa-trash"></i> {{ trans('admin.delete') }}
                        </button>
                    </form>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
