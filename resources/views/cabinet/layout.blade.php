<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Cabinet') - RISMENT</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-body antialiased bg-bg">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-white border-r border-brand-border flex-shrink-0 sticky top-0 h-screen overflow-y-auto">
            <div class="p-6 border-b border-brand-border">
                <h1 class="text-h3 font-heading gradient-brand bg-clip-text text-transparent">RISMENT</h1>
                <p class="text-body-s text-text-muted mt-1">{{ __('Client Cabinet') }}</p>
            </div>
            
            <!-- Company Selector -->
            @if(isset($currentCompany) && $currentCompany)
                @if(Auth::user()->companies->count() > 1)
                <div class="p-4 border-b border-brand-border">
                    <label class="text-body-s font-semibold text-text-muted">{{ __('Company') }}</label>
                    <form method="POST" id="company-switch-form">
                        @csrf
                        <select name="company_id" onchange="switchCompany(this.value)" class="input mt-2">
                            @foreach(Auth::user()->companies as $company)
                                <option value="{{ $company->id }}" {{ $currentCompany->id == $company->id ? 'selected' : '' }}>
                                    {{ $company->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
                @else
                <div class="p-4 border-b border-brand-border">
                    <div class="font-semibold">{{ $currentCompany->name }}</div>
                    <div class="text-body-s text-text-muted">{{ $currentCompany->email }}</div>
                    <div class="mt-2 text-body-s font-semibold {{ $currentCompany->balance < 0 ? 'text-error' : 'text-success' }}">
                        {{ __('Balance') }}: {{ $currentCompany->formatted_balance }}
                    </div>
                </div>
                @endif
            @else
            <div class="p-4 border-b border-brand-border">
                <div class="font-semibold text-text-muted">{{ __('No company selected') }}</div>
            </div>
            @endif
            
            <!-- Navigation -->
            <nav class="p-4">
                <a href="{{ route('cabinet.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-btn {{ request()->routeIs('cabinet.dashboard') ? 'bg-brand text-white' : 'hover:bg-bg-soft' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span>{{ __('Dashboard') }}</span>
                </a>
                
                <a href="{{ route('cabinet.inventory.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-btn {{ request()->routeIs('cabinet.inventory.*') ? 'bg-brand text-white' : 'hover:bg-bg-soft' }} mt-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <span>{{ __('Inventory') }}</span>
                </a>
                
                <a href="{{ route('cabinet.products.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-btn {{ request()->routeIs('cabinet.products.*') ? 'bg-brand text-white' : 'hover:bg-bg-soft' }} mt-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <span>{{ __('Products') }}</span>
                </a>
                
                <a href="{{ route('cabinet.inbounds.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-btn {{ request()->routeIs('cabinet.inbounds.*') ? 'bg-brand text-white' : 'hover:bg-bg-soft' }} mt-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                    </svg>
                    <span>{{ __('Inbounds') }}</span>
                </a>
                
                <a href="{{ route('cabinet.shipments.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-btn {{ request()->routeIs('cabinet.shipments.*') ? 'bg-brand text-white' : 'hover:bg-bg-soft' }} mt-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                    </svg>
                    <span>{{ __('Shipments') }}</span>
                </a>
                
                <a href="{{ route('cabinet.tickets.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-btn {{ request()->routeIs('cabinet.tickets.*') ? 'bg-brand text-white' : 'hover:bg-bg-soft' }} mt-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                    </svg>
                    <span>{{ __('Tickets') }}</span>
                </a>
                
                <a href="{{ route('cabinet.finance.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-btn {{ request()->routeIs('cabinet.finance.*') ? 'bg-brand text-white' : 'hover:bg-bg-soft' }} mt-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>{{ __('Finance') }}</span>
                </a>
                
                <a href="{{ route('cabinet.billing.report') }}" class="flex items-center gap-3 px-4 py-3 rounded-btn {{ request()->routeIs('cabinet.billing.*') ? 'bg-brand text-white' : 'hover:bg-bg-soft' }} mt-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>{{ __('Billing') }}</span>
                </a>

                <a href="{{ route('cabinet.sellermind.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-btn {{ request()->routeIs('cabinet.sellermind.*') ? 'bg-brand text-white' : 'hover:bg-bg-soft' }} mt-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                    </svg>
                    <span>{{ __('SellerMind') }}</span>
                </a>

                <a href="{{ route('cabinet.company.show') }}" class="flex items-center gap-3 px-4 py-3 rounded-btn {{ request()->routeIs('cabinet.company.*') ? 'bg-brand text-white' : 'hover:bg-bg-soft' }} mt-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span>{{ __('Company') }}</span>
                </a>
                
                <a href="{{ route('cabinet.profile') }}" class="flex items-center gap-3 px-4 py-3 rounded-btn {{ request()->routeIs('cabinet.profile') ? 'bg-brand text-white' : 'hover:bg-bg-soft' }} mt-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>{{ __('Profile') }}</span>
                </a>
            </nav>
            
            <!-- Language Switcher -->
            <div class="p-4 border-t border-brand-border">
                <label class="text-body-s font-semibold text-text-muted mb-2 block">{{ __('Language') }}</label>
                <form method="POST" action="{{ route('cabinet.profile.locale') }}" id="locale-form">
                    @csrf
                    @method('PUT')
                    <select name="locale" onchange="this.form.submit()" class="input w-full">
                        <option value="ru" {{ auth()->user()->locale === 'ru' ? 'selected' : '' }}>Русский</option>
                        <option value="uz" {{ auth()->user()->locale === 'uz' ? 'selected' : '' }}>O'zbek</option>
                        <option value="en" {{ auth()->user()->locale === 'en' ? 'selected' : '' }}>English</option>
                    </select>
                </form>
            </div>
            
            <!-- Logout -->
            <div class="absolute bottom-0 w-64 p-4 border-t border-brand-border bg-white">
                <form method="POST" action="{{ route('logout', ['locale' => app()->getLocale()]) }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-btn hover:bg-bg-soft text-error">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <span>{{ __('Logout') }}</span>
                    </button>
                </form>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="flex-1 overflow-auto">
            <div class="p-8">
                @if(session('success'))
                    <div class="mb-6 p-4 bg-success/10 border border-success rounded-card text-success">
                        {{ session('success') }}
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="mb-6 p-4 bg-error/10 border border-error rounded-card text-error">
                        {{ session('error') }}
                    </div>
                @endif
                
                @yield('content')
            </div>
        </main>
    </div>
    
    <script>
        function switchCompany(companyId) {
            fetch(`/cabinet/profile/switch-company/${companyId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            }).then(() => {
                window.location.reload();
            });
        }
    </script>
    @stack('scripts')
</body>
</html>
