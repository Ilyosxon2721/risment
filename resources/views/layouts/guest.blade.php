<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'RISMENT') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .auth-gradient {
            background: linear-gradient(135deg, 
                hsl(var(--color-brand-dark)) 0%, 
                hsl(var(--color-brand)) 50%, 
                hsl(var(--color-brand-light)) 100%);
            position: relative;
            overflow: hidden;
        }
        .auth-gradient::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }
        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15), 
                        0 0 0 1px rgba(255, 255, 255, 0.5) inset;
        }
        .logo-text {
            font-size: 2.5rem;
            font-weight: 800;
            color: white;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            letter-spacing: 0.05em;
            transition: all 0.3s ease;
        }
        .logo-text:hover {
            transform: scale(1.05);
            text-shadow: 0 6px 30px rgba(0, 0, 0, 0.4);
        }
        .logo-img {
            max-height: 4rem;
            filter: drop-shadow(0 0 20px rgba(255, 255, 255, 0.3));
            transition: transform 0.3s ease, filter 0.3s ease;
        }
        .logo-img:hover {
            transform: scale(1.05);
            filter: drop-shadow(0 0 30px rgba(255, 255, 255, 0.5));
        }
        .lang-btn {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        .lang-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transform: translate(-50%, -50%);
            transition: width 0.4s ease, height 0.4s ease;
        }
        .lang-btn:hover::before {
            width: 100px;
            height: 100px;
        }
        .lang-btn-active {
            background: white;
            color: hsl(var(--color-brand));
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        .lang-btn-inactive {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            backdrop-filter: blur(10px);
        }
        .lang-btn-inactive:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="font-body antialiased">
    <div class="min-h-screen flex items-center justify-center auth-gradient py-12 px-4">
        <div class="container-risment max-w-md relative z-10">
            <div class="text-center mb-8 animate-fade-in">
                <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="inline-block">
                    @php $settings = \App\Models\CompanySettings::current(); @endphp
                    @if($settings && $settings->company_logo)
                        <img src="{{ $settings->getLogoUrl() }}" alt="{{ $settings->company_name ?? 'RISMENT' }}" class="logo-img mx-auto mb-4">
                    @else
                        <h1 class="logo-text mb-4">RISMENT</h1>
                    @endif
                </a>
            </div>

            <div class="glass-card rounded-card p-8 animate-slide-up">
                {{ $slot }}
            </div>
            
            <div class="mt-8 text-center animate-fade-in" style="animation-delay: 0.2s;">
                <div class="flex gap-3 justify-center text-body-s font-medium">
                    @php
                        $currentRoute = Route::currentRouteName();
                        $currentParams = request()->route()->parameters();
                    @endphp
                    
                    <a href="{{ route($currentRoute, array_merge($currentParams, ['locale' => 'ru'])) }}" 
                       class="lang-btn px-4 py-2 rounded-btn {{ app()->getLocale() === 'ru' ? 'lang-btn-active' : 'lang-btn-inactive' }}">
                        RU
                    </a>
                    <a href="{{ route($currentRoute, array_merge($currentParams, ['locale' => 'uz'])) }}" 
                       class="lang-btn px-4 py-2 rounded-btn {{ app()->getLocale() === 'uz' ? 'lang-btn-active' : 'lang-btn-inactive' }}">
                        UZ
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
