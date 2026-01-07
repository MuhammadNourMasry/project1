@extends('layouts.dashboard')

@push('styles')
    @foreach (['index', 'statistics', 'pending', 'request-card', 'stat-card'] as $src)
        <link rel="stylesheet" href="{{ asset('css/' . $src . '.css') }}">
    @endforeach
@endpush

@section('content')
    <h2 class="title">Dashboard</h2>
    <div class="index">
        <x-statistics :stats="$stats"/>
        <x-pending-dash
            :users="$users"
            :pending-user-count="$pendingUserCount"
        />
    </div>
@endsection
