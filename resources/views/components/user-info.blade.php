{{-- <div class="user-info">
    <div class="user-profile">
        <x-button
            class="click-photo"
            :img-src="$imgSrc"
            style-class="avatar"
        />
        <span class="name">{{ $fullName }}</span>
    </div>
    <span class="role {{ $role }}">{{ $role }}</span>
</div>
 --}}
<div class="user-info">
    <div class="user-profile">
        <x-button
            class="click-photo"
            :img-src="$imgSrc"
            style-class="avatar"
            :link="'#photo-modal-' . $userId"
        />
        <span class="name">{{ $fullName }}</span>
    </div>
    <span class="role {{ $role }}">{{ $role }}</span>
</div>

