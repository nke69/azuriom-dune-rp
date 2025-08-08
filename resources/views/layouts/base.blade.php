@extends('layouts.app')

@section('title', $title ?? trans('dune-rp::messages.title'))

@push('styles')
    <link href="{{ plugin_asset('dune-rp', 'css/dune-rp.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&display=swap" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ plugin_asset('dune-rp', 'js/dune-rp.js') }}"></script>
@endpush

@section('content')
<div class="dune-container">
    @yield('dune-content')
</div>
@endsection
