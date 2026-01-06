{{-- <div class="request-card">
    <x-user-info
        :img-src="asset($user->personal_photo)"
        :full-name="$user->first_name . ' ' . $user->last_name"
        :role="$user->role"
    />

    <x-user-details
        :properties="[
            ['src_icon' => 'icons/phone-call.svg', 'text' => '+963 ' . $user->phone],
            ['src_icon' => 'icons/calendar.svg', 'text' => 'DOB: ' . $user->date_of_birth],
            ['src_icon' => 'icons/id-card.svg', 'text' => 'ID: ' . $user->id],
        ]"
    />

    <div class="Acceptance-status">
        <form method="POST" action="{{ route('user.approve', $user) }}">
            @csrf
            @method('PATCH')

            <button type="submit" class="button-style approve">
                Approve
            </button>
        </form>

        <form method="POST" action="{{ route('user.reject', $user) }}">
            @csrf
            @method('DELETE')

            <button type="submit" class="button-style reject">
                Reject
            </button>
        </form>
    </div>

</div>
 --}}

 <div class="request-card">
    <x-user-info
        :img-src="asset($user->personal_photo)"
        :full-name="$user->first_name . ' ' . $user->last_name"
        :role="$user->role"
        :user-id="$user->id"
    />

    <x-user-details
        :properties="[
            ['src_icon' => 'icons/phone-call.svg', 'text' => '+963 ' . $user->phone],
            ['src_icon' => 'icons/calendar.svg', 'text' => 'DOB: ' . $user->date_of_birth],
            ['src_icon' => 'icons/id-card.svg', 'text' => 'ID: ' . $user->id],
        ]"
    />

    <div class="Acceptance-status">
        <form method="POST" action="{{ route('user.approve', $user) }}">
            @csrf
            @method('PATCH')
            <button type="submit" class="button-style approve">Approve</button>
        </form>

        <form method="POST" action="{{ route('user.reject', $user) }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="button-style reject">Reject</button>
        </form>
    </div>
</div>


<div id="photo-modal-{{ $user->id }}" class="photo-modal">
    <a href="#" class="photo-modal__backdrop" aria-label="Close"></a>

    <div class="photo-modal__content" role="dialog" aria-modal="true">
        <img src="{{ asset($user->personal_photo) }}" alt="User photo">
    </div>
</div>

