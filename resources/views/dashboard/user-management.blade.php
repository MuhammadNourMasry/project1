@extends('layouts.dashboard')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/user-management.css') }}">
@endpush

@section('content')
    <h2 class="title">User Management</h2>
    <div class="user-management">
        <table class="users-table">
            <thead>
                <tr>
                    <th>Avatar</th>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Role</th>
                    <th style="text-align:center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>
                            <x-button
                                class="click-photo"
                                :img-src="$user->personal_photo"
                                style-class="avatar"
                            />
                        </td>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->first_name . ' ' . $user->last_name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone }}</td>
                        <td>{{ $user->role }}</td>
                        <td style="text-align: center">
                            <form method="POST" action="{{ route('user.delete', $user) }}">
                                @csrf
                                @method('DELETE')
                                <button class="delete">
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
