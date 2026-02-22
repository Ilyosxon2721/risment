<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Вход менеджера - RISMENT</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-body antialiased bg-bg">
    <div class="min-h-screen flex items-center justify-center py-12 px-4">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <h1 class="text-h2 font-heading gradient-brand bg-clip-text text-transparent">RISMENT</h1>
                <p class="text-body-m text-text-muted mt-2">Панель менеджера</p>
            </div>

            <div class="bg-white rounded-card border border-brand-border p-8">
                <h2 class="text-h3 font-heading text-brand-dark mb-6 text-center">Вход в систему</h2>

                @if(session('status'))
                    <div class="mb-4 p-3 bg-success/10 border border-success rounded text-success text-body-s">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('manager.login.submit') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="block text-body-m font-semibold text-brand-dark mb-2">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}"
                               required autofocus autocomplete="username"
                               class="input w-full" placeholder="your@email.com">
                        @error('email')
                            <p class="text-error text-body-s mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-body-m font-semibold text-brand-dark mb-2">Пароль</label>
                        <input id="password" type="password" name="password"
                               required autocomplete="current-password"
                               class="input w-full" placeholder="••••••••">
                        @error('password')
                            <p class="text-error text-body-s mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center">
                        <input id="remember" type="checkbox" name="remember"
                               class="rounded border-brand-border text-brand focus:ring-brand">
                        <label for="remember" class="ml-2 text-body-s text-brand-dark">Запомнить меня</label>
                    </div>

                    <button type="submit" class="btn btn-primary w-full text-body-l font-semibold py-3">
                        Войти
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
