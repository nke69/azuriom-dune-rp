@extends('admin.layouts.admin')

@section('title', $house->name)

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{ $house->name }}</h1>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ trans('dune-rp::admin.houses.details') }}</h3>
                    </div>
                    <div class="card-body">
                        @if($house->sigil_url)
                            <img src="{{ $house->sigil_url }}" alt="{{ $house->name }}" class="img-thumbnail mb-3" style="max-width: 200px;">
                        @endif
                        
                        <dl class="row">
                            <dt class="col-sm-3">{{ trans('dune-rp::admin.houses.fields.motto') }}</dt>
                            <dd class="col-sm-9">{{ $house->motto ?? '-' }}</dd>
                            
                            <dt class="col-sm-3">{{ trans('dune-rp::admin.houses.fields.description') }}</dt>
                            <dd class="col-sm-9">{!! $house->parseDescription() !!}</dd>
                            
                            <dt class="col-sm-3">{{ trans('dune-rp::admin.houses.fields.leader') }}</dt>
                            <dd class="col-sm-9">
                                @if($house->leader)
                                    <a href="{{ route('admin.users.edit', $house->leader) }}">{{ $house->leader->name }}</a>
                                @else
                                    -
                                @endif
                            </dd>
                            
                            <dt class="col-sm-3">{{ trans('dune-rp::admin.houses.fields.homeworld') }}</dt>
                            <dd class="col-sm-9">{{ $house->homeworld ?? '-' }}</dd>
                            
                            <dt class="col-sm-3">{{ trans('dune-rp::admin.houses.fields.color') }}</dt>
                            <dd class="col-sm-9">
                                <span class="badge" style="background-color: {{ $house->color }};">{{ $house->color }}</span>
                            </dd>
                            
                            <dt class="col-sm-3">{{ trans('dune-rp::admin.houses.fields.spice_reserves') }}</dt>
                            <dd class="col-sm-9">{{ number_format($house->spice_reserves, 2) }} tonnes</dd>
                            
                            <dt class="col-sm-3">{{ trans('dune-rp::admin.houses.fields.influence_points') }}</dt>
                            <dd class="col-sm-9">{{ number_format($house->influence_points) }}</dd>
                            
                            <dt class="col-sm-3">{{ trans('dune-rp::admin.houses.fields.is_active') }}</dt>
                            <dd class="col-sm-9">
                                @if($house->is_active)
                                    <span class="badge badge-success">{{ trans('admin.yes') }}</span>
                                @else
                                    <span class="badge badge-danger">{{ trans('admin.no') }}</span>
                                @endif
                            </dd>
                        </dl>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('dune-rp.admin.houses.edit', $house) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> {{ trans('admin.edit') }}
                        </a>
                        <form action="{{ route('dune-rp.admin.houses.destroy', $house) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('{{ trans('dune-rp::admin.houses.delete_confirm') }}')">
                                <i class="fas fa-trash"></i> {{ trans('admin.delete') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ trans('dune-rp::admin.houses.members') }}</h3>
                    </div>
                    <div class="card-body">
                        @if($house->characters->count() > 0)
                            <ul class="list-unstyled">
                                @foreach($house->characters as $character)
                                    <li class="mb-2">
                                        <a href="{{ route('dune-rp.admin.characters.show', $character) }}">
                                            {{ $character->name }}
                                        </a>
                                        @if($character->title)
                                            <small class="text-muted">{{ $character->title }}</small>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted">{{ trans('dune-rp::admin.houses.no_members') }}</p>
                        @endif
                    </div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">{{ trans('dune-rp::admin.houses.spice.title') }}</h3>
                    </div>
                    <div class="card-body">
                        <button type="button" class="btn btn-warning btn-block" data-toggle="modal" data-target="#adjustSpiceModal">
                            <i class="fas fa-coins"></i> {{ trans('dune-rp::admin.houses.spice.adjust') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajustement Épice -->
<div class="modal fade" id="adjustSpiceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('dune-rp.admin.houses.adjust-spice', $house) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">{{ trans('dune-rp::admin.houses.spice.adjust') }}</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>{{ trans('dune-rp::admin.houses.spice.current') }}: {{ number_format($house->spice_reserves, 2) }} tonnes</label>
                    </div>
                    <div class="form-group">
                        <label for="adjustment_type">{{ trans('dune-rp::admin.houses.spice.type') }}</label>
                        <select id="adjustment_type" name="adjustment_type" class="form-control" required>
                            <option value="add">{{ trans('dune-rp::admin.houses.spice.add') }}</option>
                            <option value="remove">{{ trans('dune-rp::admin.houses.spice.remove') }}</option>
                            <option value="set">{{ trans('dune-rp::admin.houses.spice.set') }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="amount">{{ trans('dune-rp::admin.houses.spice.amount') }}</label>
                        <input type="number" id="amount" name="amount" class="form-control" step="0.01" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="reason">{{ trans('dune-rp::admin.houses.spice.reason') }}</label>
                        <input type="text" id="reason" name="reason" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('admin.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ trans('admin.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
