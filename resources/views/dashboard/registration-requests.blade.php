@extends('layouts.dashboard')

@push('styles')
    @foreach (['registration-requests', 'request-card'] as $src)
        <link rel="stylesheet" href="{{ asset('css/' . $src . '.css') }}">
    @endforeach
@endpush

@section('content')
    <h2 class="title">Registration Requests</h2>
    <div class="registration-requests">
        @foreach ($users as $user)
            <x-request-card :user="$user"/>
        @endforeach
    </div>
@endsection
