<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- SEO Meta Tags --}}
    <title>@yield('title', 'RISMENT - Fulfillment для маркетплейсов Узбекистана')</title>
    <meta name="description" content="@yield('description', 'Профессиональный фулфилмент для маркетплейсов Узбекистана. FBS, FBO, DBS услуги. Хранение, сборка, доставка. Работаем с Uzum, Wildberries, Ozon.')">
    <meta name="keywords" content="фулфилмент, fulfillment, Узбекистан, маркетплейс, FBS, FBO, DBS, хранение, логистика">
    <link rel="canonical" href="{{ url()->current() }}">
    
    {{-- Open Graph / Facebook --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', 'RISMENT - Fulfillment для маркетплейсов Узбекистана')">
    <meta property="og:description" content="@yield('description', 'Профессиональный фулфилмент для маркетплейсов Узбекистана. FBS, FBO, DBS услуги. Хранение, сборка, доставка. Работаем с Uzum, Wildberries, Ozon.')">
    <meta property="og:image" content="@yield('og_image', asset('images/og-image.jpg'))">
    <meta property="og:locale" content="{{ app()->getLocale() === 'ru' ? 'ru_RU' : 'uz_UZ' }}">
    <meta property="og:site_name" content="RISMENT">
    
    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="@yield('title', 'RISMENT - Fulfillment для маркетплейсов Узбекистана')">
    <meta name="twitter:description" content="@yield('description', 'Профессиональный фулфилмент для маркетплейсов Узбекистана. FBS, FBO, DBS услуги.')">
    <meta name="twitter:image" content="@yield('og_image', asset('images/og-image.jpg'))">
    
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
    <header class="sticky top-0 z-50 bg-white border-b border-brand-border">
        <div class="container-risment flex justify-between items-center py-4">
            <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="flex items-center">
                @php $headerSettings = \App\Models\CompanySettings::current(); @endphp
                @if($headerSettings && $headerSettings->company_logo)
                    <img src="{{ $headerSettings->getLogoUrl() }}" alt="{{ $headerSettings->company_name ?? 'RISMENT' }}" class="h-10">
                @else
                    <span class="text-h3 font-heading text-brand font-bold">RISMENT</span>
                @endif
            </a>
            
            <nav class="flex gap-8">
                <a href="{{ route('services.index', ['locale' => app()->getLocale()]) }}" class="text-body-m hover:text-brand transition">{{ __('Services') }}</a>
                <a href="{{ route('pricing', ['locale' => app()->getLocale()]) }}" class="text-body-m hover:text-brand transition">{{ __('Pricing') }}</a>
                <a href="{{ route('calculator', ['locale' => app()->getLocale()]) }}" class="text-body-m hover:text-brand transition">{{ __('Calculator') }}</a>
                <a href="{{ route('faq', ['locale' => app()->getLocale()]) }}" class="text-body-m hover:text-brand transition">{{ __('FAQ') }}</a>
                <a href="{{ route('contacts', ['locale' => app()->getLocale()]) }}" class="text-body-m hover:text-brand transition">{{ __('Contacts') }}</a>
            </nav>
            
            <div class="flex items-center gap-4">
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
                </div>
                
            <!-- Auth buttons -->
            <div class="flex items-center gap-3">
                @guest
                    <a href="{{ route('login', ['locale' => app()->getLocale()]) }}" class="btn btn-secondary text-sm">
                        {{ __('Login') }}
                    </a>
                    <a href="{{ route('register', ['locale' => app()->getLocale()]) }}" class="btn btn-primary text-sm">
                        {{ __('Register') }}
                    </a>
                @else
                    <a href="{{ route('cabinet.dashboard') }}" class="btn btn-primary text-sm">
                        {{ __('Cabinet') }}
                    </a>
                @endguest
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
    
    <!-- Alpine.js for interactive components -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
