@extends('admin.layouts.admin')

@section('title', trans('dune-rp::admin.characters.create'))

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{ trans('dune-rp::admin.characters.create') }}</h1>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <form action="{{ route('dune-rp.admin.characters.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="user_id">{{ trans('dune-rp::admin.characters.fields.player') }}</label>
                                <select id="user_id" name="user_id" class="form-control @error('user_id') is-invalid @enderror" required>
                                    <option value="">{{ trans('dune-rp::admin.select_player') }}</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">{{ trans('dune-rp::admin.characters.fields.name') }}</label>
                                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name') }}" required>
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="title">{{ trans('dune-rp::admin.characters.fields.title') }}</label>
                                <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror" 
                                       value="{{ old('title') }}">
                                @error('title')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="house_id">{{ trans('dune-rp::admin.characters.fields.house') }}</label>
                                <select id="house_id" name="house_id" class="form-control @error('house_id') is-invalid @enderror">
                                    <option value="">{{ trans('dune-rp::admin.no_house') }}</option>
                                    @foreach($houses as $house)
                                        <option value="{{ $house->id }}" {{ old('house_id') == $house->id ? 'selected' : '' }}>
                                            {{ $house->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('house_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="biography">{{ trans('dune-rp::admin.characters.fields.biography') }}</label>
                        <textarea id="biography" name="biography" class="form-control @error('biography') is-invalid @enderror" 
                                  rows="5">{{ old('biography') }}</textarea>
                        @error('biography')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="birthworld">{{ trans('dune-rp::admin.characters.fields.birthworld') }}</label>
                                <input type="text" id="birthworld" name="birthworld" class="form-control @error('birthworld') is-invalid @enderror" 
                                       value="{{ old('birthworld') }}">
                                @error('birthworld')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="age">{{ trans('dune-rp::admin.characters.fields.age') }}</label>
                                <input type="number" id="age" name="age" class="form-control @error('age') is-invalid @enderror" 
                                       value="{{ old('age') }}" min="1" max="500">
                                @error('age')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status">{{ trans('dune-rp::admin.characters.fields.status') }}</label>
                                <select id="status" name="status" class="form-control @error('status') is-invalid @enderror" required>
                                    @foreach(\Azuriom\Plugin\DuneRp\Models\Character::STATUSES as $key => $label)
                                        <option value="{{ $key }}" {{ old('status', 'alive') == $key ? 'selected' : '' }}>
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
                                <option value="{{ $level }}" {{ old('spice_addiction_level', 0) == $level ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('spice_addiction_level')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="avatar">{{ trans('dune-rp::admin.characters.fields.avatar') }}</label>
                        <input type="file" id="avatar" name="avatar" class="form-control-file @error('avatar') is-invalid @enderror" accept="image/*">
                        @error('avatar')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_public" name="is_public" value="1" 
                                   {{ old('is_public', true) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_public">
                                {{ trans('dune-rp::admin.characters.fields.is_public') }}
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_approved" name="is_approved" value="1" 
                                   {{ old('is_approved', false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_approved">
                                {{ trans('dune-rp::admin.characters.fields.is_approved') }}
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> {{ trans('admin.save') }}
                    </button>
                    <a href="{{ route('dune-rp.admin.characters.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> {{ trans('admin.cancel') }}
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
