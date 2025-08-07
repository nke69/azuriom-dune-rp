@extends('admin.layouts.admin')

@section('title', trans('dune-rp::admin.economy.title'))

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>
                    <i class="fas fa-coins"></i>
                    {{ trans('dune-rp::admin.economy.title') }}
                </h1>
            </div>
            <div class="col-sm-6">
                <div class="float-sm-right">
                    <a href="{{ route('dune-rp.admin.economy.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        {{ trans('dune-rp::admin.economy.create') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        {{-- Filtres --}}
        <form method="GET" action="{{ route('dune-rp.admin.economy.index') }}">
            <div class="row mb-3">
                <div class="col-md-4">
                    <input name="search" type="text" class="form-control" placeholder="{{ trans('dune-rp::admin.economy.search_placeholder') }}" value="{{ request('search') }}">
                </div>
            </div>
        </form>

        {{-- Liste des éléments économiques --}}
        @if($economies->count() > 0)
            <div class="row">
                @foreach($economies as $economy)
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title">{{ $economy->name }}</h5>
                                <p class="card-text">{{ $economy->description }}</p>
                                <span class="badge badge-success">{{ $economy->type }}</span>
                                <div class="mt-2">
                                    <a href="{{ route('dune-rp.admin.economy.show', $economy) }}" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('dune-rp.admin.economy.edit', $economy) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('dune-rp.admin.economy.destroy', $economy) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('{{ trans('dune-rp::admin.economy.confirm_delete') }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            {{ $economies->appends(request()->query())->links() }}
        @else
            <div class="alert alert-info">
                {{ trans('dune-rp::admin.economy.no_economy') }}
            </div>
        @endif
    </div>
</div>
@endsection
