<div class="pending">
    <div class="pending-header">
        <p>Pending Registration Requests</p>
        <span>{{ $pendingUserCount }} Pending</span>
    </div>
    <div class="pending-body">
        @foreach ($users as $user)
            <x-request-card :user="$user"/>
        @endforeach
    </div>
    <a class="view-requests-btn" href="{{ route('registration-requests') }}">View All Requests</a>
</div>
