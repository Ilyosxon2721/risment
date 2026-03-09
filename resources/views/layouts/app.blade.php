<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- SEO Meta Tags --}}
    <title>@yield('title', __('RISMENT - Fulfillment for Uzbekistan marketplaces'))</title>
    <meta name="description" content="@yield('description', __('Professional fulfillment for Uzbekistan marketplaces. FBS, FBO, DBS services. Storage, assembly, delivery. Working with Uzum, Wildberries, Ozon.'))">
    <meta name="keywords" content="{{ __('fulfillment, Uzbekistan, marketplace, FBS, FBO, DBS, storage, logistics') }}">
    <link rel="canonical" href="{{ url()->current() }}">
    
    {{-- Open Graph / Facebook --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', __('RISMENT - Fulfillment for Uzbekistan marketplaces'))">
    <meta property="og:description" content="@yield('description', __('Professional fulfillment for Uzbekistan marketplaces. FBS, FBO, DBS services. Storage, assembly, delivery. Working with Uzum, Wildberries, Ozon.'))">
    <meta property="og:image" content="@yield('og_image', asset('images/og-image.jpg'))">
    <meta property="og:locale" content="{{ app()->getLocale() === 'ru' ? 'ru_RU' : (app()->getLocale() === 'en' ? 'en_US' : 'uz_UZ') }}">
    <meta property="og:site_name" content="RISMENT">
    
    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="@yield('title', __('RISMENT - Fulfillment for Uzbekistan marketplaces'))">
    <meta name="twitter:description" content="@yield('description', __('Professional fulfillment for Uzbekistan marketplaces. FBS, FBO, DBS services.'))">
    <meta name="twitter:image" content="@yield('og_image', asset('images/og-image.jpg'))">

    {{-- PWA --}}
    <link rel="manifest" href="/manifest.webmanifest">
    <meta name="theme-color" content="#CB4FE4">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Risment">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">

    {{-- Google Analytics --}}
    @if(config('services.google_analytics.id'))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google_analytics.id') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ config('services.google_analytics.id') }}');
    </script>
    @endif
    
    {{-- Yandex Metrika --}}
    @if(config('services.yandex_metrika.id'))
    <script type="text/javascript">
        (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
        m[i].l=1*new Date();
        for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
        k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
        (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");
        
        ym({{ config('services.yandex_metrika.id') }}, "init", {
            clickmap:true,
            trackLinks:true,
            accurateTrackBounce:true,
            webvisor:true
        });
    </script>
    <noscript><div><img src="https://mc.yandex.ru/watch/{{ config('services.yandex_metrika.id') }}" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
    @endif
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-body antialiased bg-bg">
    <!-- Header -->
    <header class="sticky top-0 z-50 bg-white border-b border-brand-border" x-data="{ mobileMenuOpen: false }">
        <div class="container-risment flex justify-between items-center py-4">
            <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="flex items-center flex-shrink-0">
                @php $headerSettings = \App\Models\CompanySettings::current(); @endphp
                @if($headerSettings && $headerSettings->company_logo)
                    <img src="{{ $headerSettings->getLogoUrl() }}" alt="{{ $headerSettings->company_name ?? 'RISMENT' }}" class="h-10 max-w-full">
                @else
                    <span class="text-h3 font-heading text-brand font-bold">RISMENT</span>
                @endif
            </a>

            <!-- Desktop Navigation -->
            <nav class="hidden lg:flex gap-8">
                <a href="{{ route('services.index', ['locale' => app()->getLocale()]) }}" class="text-body-m hover:text-brand transition">{{ __('Services') }}</a>
                <a href="{{ route('pricing', ['locale' => app()->getLocale()]) }}" class="text-body-m hover:text-brand transition">{{ __('Pricing') }}</a>
                <a href="{{ route('calculator', ['locale' => app()->getLocale()]) }}" class="text-body-m hover:text-brand transition">{{ __('Calculator') }}</a>
                <a href="{{ route('faq', ['locale' => app()->getLocale()]) }}" class="text-body-m hover:text-brand transition">{{ __('FAQ') }}</a>
                <a href="{{ route('contacts', ['locale' => app()->getLocale()]) }}" class="text-body-m hover:text-brand transition">{{ __('Contacts') }}</a>
            </nav>

            <div class="hidden lg:flex items-center gap-4">
                <!-- Locale Switcher -->
                <div class="flex gap-2">
                    <a href="{{ route(Route::currentRouteName(), array_merge(request()->route()->parameters(), ['locale' => 'ru'])) }}"
                       class="px-3 py-1 rounded-btn {{ app()->getLocale() === 'ru' ? 'bg-brand text-white' : 'bg-bg-soft hover:bg-bg-soft' }}">
                        RU
                    </a>
                    <a href="{{ route(Route::currentRouteName(), array_merge(request()->route()->parameters(), ['locale' => 'uz'])) }}"
                       class="px-3 py-1 rounded-btn {{ app()->getLocale() === 'uz' ? 'bg-brand text-white' : 'bg-bg-soft hover:bg-bg-soft' }}">
                        UZ
                    </a>
                    <a href="{{ route(Route::currentRouteName(), array_merge(request()->route()->parameters(), ['locale' => 'en'])) }}"
                       class="px-3 py-1 rounded-btn {{ app()->getLocale() === 'en' ? 'bg-brand text-white' : 'bg-bg-soft hover:bg-bg-soft' }}">
                        EN
                    </a>
                </div>

                <!-- Auth buttons -->
                @php
                    $navUser = Auth::user() ?? Auth::guard('manager')->user();
                @endphp
                <div class="flex items-center gap-3">
                    @if($navUser)
                        @if($navUser->hasAnyRole(['manager', 'admin']))
                        <a href="/manager/" class="btn btn-secondary text-sm">
                            {{ __('Manager') }}
                        </a>
                        @endif
                        @if($navUser->hasRole('admin'))
                        <a href="/admin/" class="btn btn-secondary text-sm">
                            {{ __('Admin') }}
                        </a>
                        @endif
                        @if(Auth::check())
                        <a href="{{ route('cabinet.dashboard') }}" class="btn btn-primary text-sm">
                            {{ __('Cabinet') }}
                        </a>
                        @endif
                    @else
                        <a href="{{ route('login', ['locale' => app()->getLocale()]) }}" class="btn btn-secondary text-sm">
                            {{ __('Login') }}
                        </a>
                        <a href="{{ route('register', ['locale' => app()->getLocale()]) }}" class="btn btn-primary text-sm">
                            {{ __('Register') }}
                        </a>
                    @endif
                </div>
            </div>

            <!-- Mobile Hamburger Button -->
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 -mr-2 rounded-btn hover:bg-bg-soft min-w-[44px] min-h-[44px] flex items-center justify-center" aria-label="Toggle menu">
                <svg x-show="!mobileMenuOpen" class="w-6 h-6 text-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
                <svg x-show="mobileMenuOpen" x-cloak class="w-6 h-6 text-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Mobile Menu Dropdown -->
        <div
            x-show="mobileMenuOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            x-cloak
            class="lg:hidden border-t border-brand-border bg-white"
        >
            <nav class="container-risment py-4 space-y-1">
                <a href="{{ route('services.index', ['locale' => app()->getLocale()]) }}" class="block px-4 py-3 rounded-btn text-body-m hover:bg-bg-soft transition min-h-[44px] flex items-center">{{ __('Services') }}</a>
                <a href="{{ route('pricing', ['locale' => app()->getLocale()]) }}" class="block px-4 py-3 rounded-btn text-body-m hover:bg-bg-soft transition min-h-[44px] flex items-center">{{ __('Pricing') }}</a>
                <a href="{{ route('calculator', ['locale' => app()->getLocale()]) }}" class="block px-4 py-3 rounded-btn text-body-m hover:bg-bg-soft transition min-h-[44px] flex items-center">{{ __('Calculator') }}</a>
                <a href="{{ route('faq', ['locale' => app()->getLocale()]) }}" class="block px-4 py-3 rounded-btn text-body-m hover:bg-bg-soft transition min-h-[44px] flex items-center">{{ __('FAQ') }}</a>
                <a href="{{ route('contacts', ['locale' => app()->getLocale()]) }}" class="block px-4 py-3 rounded-btn text-body-m hover:bg-bg-soft transition min-h-[44px] flex items-center">{{ __('Contacts') }}</a>
            </nav>

            <!-- Mobile Locale Switcher -->
            <div class="container-risment pb-4 flex gap-2">
                <a href="{{ route(Route::currentRouteName(), array_merge(request()->route()->parameters(), ['locale' => 'ru'])) }}"
                   class="px-3 py-2 rounded-btn min-h-[44px] flex items-center justify-center {{ app()->getLocale() === 'ru' ? 'bg-brand text-white' : 'bg-bg-soft' }}">
                    RU
                </a>
                <a href="{{ route(Route::currentRouteName(), array_merge(request()->route()->parameters(), ['locale' => 'uz'])) }}"
                   class="px-3 py-2 rounded-btn min-h-[44px] flex items-center justify-center {{ app()->getLocale() === 'uz' ? 'bg-brand text-white' : 'bg-bg-soft' }}">
                    UZ
                </a>
                <a href="{{ route(Route::currentRouteName(), array_merge(request()->route()->parameters(), ['locale' => 'en'])) }}"
                   class="px-3 py-2 rounded-btn min-h-[44px] flex items-center justify-center {{ app()->getLocale() === 'en' ? 'bg-brand text-white' : 'bg-bg-soft' }}">
                    EN
                </a>
            </div>

            <!-- Mobile Auth buttons -->
            @php
                $navUser = $navUser ?? Auth::user() ?? Auth::guard('manager')->user();
            @endphp
            <div class="container-risment pb-4 flex flex-col gap-2">
                @if($navUser)
                    @if($navUser->hasAnyRole(['manager', 'admin']))
                    <a href="/manager/" class="btn btn-secondary text-sm w-full text-center min-h-[44px] flex items-center justify-center">
                        {{ __('Manager') }}
                    </a>
                    @endif
                    @if(Auth::check())
                    <a href="{{ route('cabinet.dashboard') }}" class="btn btn-primary text-sm w-full text-center min-h-[44px] flex items-center justify-center">
                        {{ __('Cabinet') }}
                    </a>
                    @endif
                @else
                    <a href="{{ route('login', ['locale' => app()->getLocale()]) }}" class="btn btn-secondary text-sm w-full text-center min-h-[44px] flex items-center justify-center">
                        {{ __('Login') }}
                    </a>
                    <a href="{{ route('register', ['locale' => app()->getLocale()]) }}" class="btn btn-primary text-sm w-full text-center min-h-[44px] flex items-center justify-center">
                        {{ __('Register') }}
                    </a>
                @endif
            </div>
        </div>
    </header>
    
    <main>
        @yield('content')
    </main>
    
    <!-- Footer -->
    <footer class="bg-text py-12 mt-20 text-white">
        <div class="container-risment">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-h4 font-heading mb-4">RISMENT</h3>
                    <p class="text-body-s">{{ __('Professional fulfillment for Uzbekistan marketplaces') }}</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-3">{{ __('Services') }}</h4>
                    <ul class="space-y-2 text-body-s">
                        <li><a href="{{ route('services.index', ['locale' => app()->getLocale()]) }}" class="hover:text-brand transition">FBS</a></li>
                        <li><a href="{{ route('services.index', ['locale' => app()->getLocale()]) }}" class="hover:text-brand transition">DBS</a></li>
                        <li><a href="{{ route('services.index', ['locale' => app()->getLocale()]) }}" class="hover:text-brand transition">FBO</a></li>
                        <li><a href="{{ route('services.marketplace', ['locale' => app()->getLocale()]) }}" class="hover:text-brand transition">{{ __('Marketplace Management') }}</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-3">{{ __('Company') }}</h4>
                    <ul class="space-y-2 text-body-s">
                        <li><a href="{{ route('about', ['locale' => app()->getLocale()]) }}" class="hover:text-brand transition">{{ __('About') }}</a></li>
                        <li><a href="{{ route('sla', ['locale' => app()->getLocale()]) }}" class="hover:text-brand transition">{{ __('SLA') }}</a></li>
                        <li><a href="{{ route('contacts', ['locale' => app()->getLocale()]) }}" class="hover:text-brand transition">{{ __('Contacts') }}</a></li>
                    </ul>
                </div>
                <div>
                    @php $settings = \App\Models\CompanySettings::current(); @endphp
                    <h4 class="font-semibold mb-3">{{ __('Contacts') }}</h4>
                    <ul class="space-y-2 text-body-s">
                        <li>{{ $settings->phone ?? '+998 (90) 123-45-67' }}</li>
                        <li>{{ $settings->email ?? 'info@risment.uz' }}</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-white/20 mt-8 pt-8 text-center text-body-s">
                <p>&copy; {{ date('Y') }} {{ $settings->company_name ?? 'RISMENT' }}. {{ __('All rights reserved') }}.</p>
            </div>
        </div>
    </footer>
    
    <!-- Live Chat Widget -->
    <x-live-chat-widget />
    
    <!-- Alpine.js for interactive components -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then((reg) => {
                        reg.addEventListener('updatefound', () => {
                            const newWorker = reg.installing;
                            if (newWorker) {
                                newWorker.addEventListener('statechange', () => {
                                    if (newWorker.state === 'activated' && navigator.serviceWorker.controller) {
                                        if (confirm('Доступна новая версия приложения. Обновить?')) {
                                            window.location.reload();
                                        }
                                    }
                                });
                            }
                        });
                    })
                    .catch((err) => console.log('[SW] Registration failed:', err));
            });
        }
    </script>
    @stack('scripts')
</body>
</html>
