<?php
    $navItems = [
        [
            'icon'       => 'icons/dashboard.svg',
            'icon_hover' => 'icons/dashboardhover.svg',
            'text'           => 'Dashboard',
            'route'           => 'index',
        ],
        [
            'icon'       => 'icons/user.svg',
            'icon_hover' => 'icons/userhover.svg',
            'text'           => 'User Management',
            'route'           => 'user-management',
        ],
        [
            'icon'       => 'icons/quote-request.svg',
            'icon_hover' => 'icons/quote-requesthover.svg',
            'text'           => 'Registration Requests',
            'route'           => 'registration-requests',
        ],
    ];
?>

<aside class = "side-bar">
    <div class = "logo-app">
        <img
            src="{{ Storage::url('icons/rent.svg') }}"
            width="40" height="40"
        />
        <h1 style = "color: #3babf6; font-family: Poppins;">RentHub<h1>
    </div>

    <nav class = "side-bar__nav">
        <ul class="nav-items">
            @foreach($navItems as $navBtn)
                <x-nav-item
                    class="nav-btn {{ request()->routeIs($navBtn['route']) ? 'active' : ''}}"
                    :src-icon="Storage::url($navBtn['icon'])"
                    :link="route($navBtn['route'])"
                    :src-icon-hover="Storage::url($navBtn['icon_hover'])"
                >
                    {{ $navBtn['text'] }}
                </x-nav-item>
            @endforeach
        </ul>
    </nav>

    <form class="logout-form" method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="logout-btn">
            Logout
        </button>
    </form>
</aside>
