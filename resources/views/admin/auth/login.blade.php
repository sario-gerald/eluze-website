<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Eluze Admin | Sign In</title>
    @if (file_exists(public_path('hot')) || file_exists(public_path('build/manifest.json')))
        @vite(['public/css/admin/auth.css'])
    @else
        <link rel="stylesheet" href="{{ asset('css/admin/auth.css') }}">
    @endif
</head>
<body>
    <main class="auth-shell">
        <section class="auth-panel" aria-labelledby="login-heading">
            <a class="brand-mark" href="{{ route('admin.login') }}" aria-label="Eluze admin login">
                <img src="{{ asset('images/eluze_logo.png') }}" alt="Eluze">
            </a>
            <p class="eyebrow">Admin Access</p>
            <h1 id="login-heading">Sign in to manage orders</h1>

            <form class="auth-form" method="POST" action="{{ route('admin.login.store') }}">
                @csrf

                <div class="field">
                    <label for="email">Email address</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
                    @error('email')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required>
                    @error('password')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <label class="remember-field">
                    <input name="remember" type="checkbox" value="1">
                    <span>Keep me signed in</span>
                </label>

                <button class="button button--primary" type="submit">Sign In</button>
            </form>
        </section>
    </main>
</body>
</html>
