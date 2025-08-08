@extends('admin.layouts.admin')

@section('title', $character->name)

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{ $character->name }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.home') }}">{{ trans('admin.nav.dashboard') }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dune-rp.characters.index') }}">{{ trans('dune-rp::admin.characters.title') }}</a>
                    </li>
                    <li class="breadcrumb-item active">{{ $character->name }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3">
                {{-- Character Avatar --}}
                <div class="card">
                    <div class="card-body text-center">
                        @if($character->avatar_url)
                            <img src="{{ $character->avatar_url }}" alt="{{ $character->name }}" class="img-fluid rounded" style="max-width: 200px;">
                        @else
                            <div style="width: 150px; height: 150px; margin: 0 auto; background: #f0f0f0; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-user" style="font-size: 3rem; color: #999;"></i>
                            </div>
                        @endif
                        
                        <h4 class="mt-3">{{ $character->name }}</h4>
                        @if($character->title)
                            <p class="text-muted">{{ $character->title }}</p>
                        @endif
                        
                        {{-- Status Badges --}}
                        <div class="mt-3">
                            @if($character->is_approved)
                                <span class="badge badge-success">{{ trans('dune-rp::admin.characters.approved') }}</span>
                            @else
                                <span class="badge badge-warning">{{ trans('dune-rp::admin.characters.pending') }}</span>
                            @endif
                            
                            @if($character->is_public)
                                <span class="badge badge-info">{{ trans('dune-rp::admin.characters.public') }}</span>
                            @else
                                <span class="badge badge-secondary">{{ trans('dune-rp::admin.characters.private') }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ trans('dune-rp::admin.actions') }}</h3>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('admin.dune-rp.characters.edit', $character) }}" class="btn btn-primary btn-block">
                            <i class="fas fa-edit"></i> {{ trans('admin.edit') }}
                        </a>
                        
                        @if(!$character->is_approved)
                            <form action="{{ route('admin.dune-rp.characters.approve', $character) }}" method="POST" class="mt-2">
                                @csrf
                                <button type="submit" class="btn btn-success btn-block">
                                    <i class="fas fa-check"></i> {{ trans('dune-rp::admin.characters.approve') }}
                                </button>
                            </form>
                            
                            <form action="{{ route('admin.dune-rp.characters.reject', $character) }}" method="POST" class="mt-2">
                                @csrf
                                <button type="submit" class="btn btn-warning btn-block">
                                    <i class="fas fa-times"></i> {{ trans('dune-rp::admin.characters.reject') }}
                                </button>
                            </form>
                        @endif
                        
                        <form action="{{ route('admin.dune-rp.characters.destroy', $character) }}" method="POST" class="mt-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-block" 
                                    onclick="return confirm('{{ trans('dune-rp::admin.characters.delete_confirm') }}')">
                                <i class="fas fa-trash"></i> {{ trans('admin.delete') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                {{-- Character Details --}}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ trans('dune-rp::admin.characters.details') }}</h3>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-3">{{ trans('dune-rp::admin.characters.fields.player') }}</dt>
                            <dd class="col-sm-9">
                                <a href="{{ route('admin.users.edit', $character->user) }}">
                                    {{ $character->user->name }}
                                </a>
                            </dd>

                            <dt class="col-sm-3">{{ trans('dune-rp::admin.characters.fields.house') }}</dt>
                            <dd class="col-sm-9">
                                @if($character->house)
                                    <a href="{{ route('admin.dune-rp.houses.show', $character->house) }}">
                                        {{ $character->house->name }}
                                    </a>
                                @else
                                    <span class="text-muted">{{ trans('dune-rp::admin.no_house') }}</span>
                                @endif
                            </dd>

                            <dt class="col-sm-3">{{ trans('dune-rp::admin.characters.fields.status') }}</dt>
                            <dd class="col-sm-9">
                                <span class="badge badge-{{ $character->status === 'alive' ? 'success' : ($character->status === 'deceased' ? 'danger' : 'warning') }}">
                                    {{ $character->getStatusName() }}
                                </span>
                            </dd>

                            <dt class="col-sm-3">{{ trans('dune-rp::admin.characters.fields.birthworld') }}</dt>
                            <dd class="col-sm-9">{{ $character->birthworld ?? '-' }}</dd>

                            <dt class="col-sm-3">{{ trans('dune-rp::admin.characters.fields.age') }}</dt>
                            <dd class="col-sm-9">{{ $character->age ?? '-' }}</dd>

                            <dt class="col-sm-3">{{ trans('dune-rp::admin.characters.fields.spice_addiction') }}</dt>
                            <dd class="col-sm-9">
                                <div class="progress" style="height: 20px; max-width: 200px;">
                                    @php
                                        $percentage = ($character->spice_addiction_level / 4) * 100;
                                        $color = $character->spice_addiction_level <= 1 ? 'success' : 
                                                ($character->spice_addiction_level <= 2 ? 'warning' : 'danger');
                                    @endphp
                                    <div class="progress-bar bg-{{ $color }}" role="progressbar" 
                                         style="width: {{ $percentage }}%">
                                        {{ $character->getAddictionLevelName() }}
                                    </div>
                                </div>
                            </dd>

                            <dt class="col-sm-3">{{ trans('dune-rp::admin.characters.fields.abilities') }}</dt>
                            <dd class="col-sm-9">
                                @if($character->special_abilities && count($character->special_abilities) > 0)
                                    @foreach($character->special_abilities as $ability)
                                        <span class="badge badge-primary mr-1">
                                            {{ \Azuriom\Plugin\DuneRp\Models\Character::SPECIAL_ABILITIES[$ability] ?? $ability }}
                                        </span>
                                    @endforeach
                                @else
                                    <span class="text-muted">{{ trans('dune-rp::admin.characters.no_abilities') }}</span>
                                @endif
                            </dd>

                            <dt class="col-sm-3">{{ trans('dune-rp::admin.characters.fields.created_at') }}</dt>
                            <dd class="col-sm-9">{{ $character->created_at->format('d/m/Y H:i') }}</dd>

                            <dt class="col-sm-3">{{ trans('dune-rp::admin.characters.fields.updated_at') }}</dt>
                            <dd class="col-sm-9">{{ $character->updated_at->format('d/m/Y H:i') }}</dd>
                        </dl>
                    </div>
                </div>

                {{-- Biography --}}
                @if($character->biography)
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ trans('dune-rp::admin.characters.fields.biography') }}</h3>
                    </div>
                    <div class="card-body">
                        {!! $character->parseBiography() !!}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
