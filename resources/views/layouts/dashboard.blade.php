<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Dashboard</title>

        <style>
            * {
                margin: 0;
                padding: 0;
            }
        </style>

        @foreach (['container', 'side-bar', 'content'] as $src)
            <link rel="stylesheet" href="{{ asset('css/' . $src . '.css') }}">
        @endforeach

        @stack('styles')
    </head>

    <body>
        <div class="container">
            @include('partials.side-bar')
            <div class="content">
                <video autoplay muted loop playsinline class="cenamic">
                    <source src="{{ Storage::url('video/cenamic.mp4') }}" type="video/mp4">
                </video>
                @yield('content')
            </div>
        </div>

    </body>
</html>
