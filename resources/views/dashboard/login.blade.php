<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">

</head>
    <body>
        <div class="login">
            <video autoplay muted loop playsinline class="cenamic">
                <source src="{{ Storage::url('video/cenamic2.mp4') }}" type="video/mp4">
            </video>

            <form class="login-card" method="POST" action="/login">
                @csrf

                <div class="logo-app">
                    <img src="{{ Storage::url('icons/rent.svg') }}" width="42" height="42">
                    <h1>RentHub</h1>
                </div>

                @if ($errors->any())
                    <div class="login-error">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <input type="email" name="email" placeholder="Email address" required>
                <input type="password" name="password" placeholder="Password" required>

                <button type="submit">Login</button>
            </form>

        </div>
    </body>
</html>
