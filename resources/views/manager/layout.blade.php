<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- PWA --}}
    <link rel="manifest" href="/manifest.webmanifest">
    <meta name="theme-color" content="#CB4FE4">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Risment">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">
    @if(config('webpush.vapid.public_key'))
    <meta name="vapid-public-key" content="{{ config('webpush.vapid.public_key') }}">
    @endif

    <title>@yield('title', 'Manager') - RISMENT</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-body antialiased bg-bg">
    <div class="min-h-screen flex" x-data="{ sidebarOpen: false }">
        <!-- Sidebar (mobile overlay + desktop static) -->
        <div
            x-show="sidebarOpen"
            x-transition:enter="transition-opacity ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-40 bg-black/50 md:hidden"
            @click="sidebarOpen = false"
        ></div>
        <aside
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-brand-border flex-shrink-0 h-screen overflow-y-auto transform transition-transform duration-300 ease-in-out md:translate-x-0 md:static md:w-64"
        >
            <div class="p-6 border-b border-brand-border">
                <h1 class="text-h3 font-heading gradient-brand bg-clip-text text-transparent">RISMENT</h1>
                <p class="text-body-s text-text-muted mt-1">{{ __('Manager Panel') }}</p>
            </div>

            <!-- Company Selector -->
            @if(isset($managerCompany) && $managerCompany)
                @if(isset($managedCompanies) && $managedCompanies->count() > 1)
                <div class="p-4 border-b border-brand-border">
                    <label class="text-body-s font-semibold text-text-muted">{{ __('Company') }}</label>
                    <select onchange="switchManagerCompany(this.value)" class="input mt-2 w-full">
                        @foreach($managedCompanies as $mc)
                            <option value="{{ $mc->id }}" {{ $managerCompany->id == $mc->id ? 'selected' : '' }}>
                                {{ $mc->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @else
                <div class="p-4 border-b border-brand-border">
                    <div class="font-semibold">{{ $managerCompany->name }}</div>
                    <div class="text-body-s text-text-muted">{{ $managerCompany->email }}</div>
                </div>
                @endif
            @endif

            <!-- Navigation -->
            <nav class="p-4">
                <a href="{{ route('manager.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-btn {{ request()->routeIs('manager.dashboard') ? 'bg-brand text-white' : 'hover:bg-bg-soft' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span>{{ __('Home') }}</span>
                </a>

                <a href="{{ route('manager.tasks.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-btn {{ request()->routeIs('manager.tasks.*') ? 'bg-brand text-white' : 'hover:bg-bg-soft' }} mt-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                    <span>{{ __('Tasks') }}</span>
                </a>

                @php $pendingCount = isset($managerCompany) ? \App\Models\ManagerTask::forCompany($managerCompany->id)->pending()->count() : 0; @endphp
                <a href="{{ route('manager.confirmations.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-btn {{ request()->routeIs('manager.confirmations.*') ? 'bg-brand text-white' : 'hover:bg-bg-soft' }} mt-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>{{ __('Confirmations') }}</span>
                    @if($pendingCount > 0)
                        <span class="ml-auto bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $pendingCount }}</span>
                    @endif
                </a>

                <a href="{{ route('manager.billing.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-btn {{ request()->routeIs('manager.billing.*') ? 'bg-brand text-white' : 'hover:bg-bg-soft' }} mt-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>{{ __('Billing') }}</span>
                </a>

                <div class="mt-4 pt-4 border-t border-brand-border">
                    <p class="px-4 text-body-xs text-text-muted font-semibold uppercase tracking-wider mb-2">{{ __('Warehouse') }}</p>
                </div>

                <a href="{{ route('manager.inventory.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-btn {{ request()->routeIs('manager.inventory.*') ? 'bg-brand text-white' : 'hover:bg-bg-soft' }} mt-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <span>{{ __('Inventory') }}</span>
                </a>

                <a href="{{ route('manager.shipments.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-btn {{ request()->routeIs('manager.shipments.*') ? 'bg-brand text-white' : 'hover:bg-bg-soft' }} mt-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                    </svg>
                    <span>{{ __('Shipments') }}</span>
                </a>

                <a href="{{ route('manager.inbounds.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-btn {{ request()->routeIs('manager.inbounds.*') ? 'bg-brand text-white' : 'hover:bg-bg-soft' }} mt-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    <span>{{ __('Inbounds') }}</span>
                </a>
            </nav>

            <!-- Logout -->
            <div class="absolute bottom-0 w-full p-4 border-t border-brand-border bg-white">
                <form method="POST" action="{{ route('manager.logout') }}">
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

        <!-- Mobile Top Bar -->
        <div class="md:hidden fixed top-0 left-0 right-0 z-30 bg-white shadow-sm flex items-center h-14">
            <button @click="sidebarOpen = true" class="p-4 text-text-muted hover:text-text-default" aria-label="Open sidebar">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
            <span class="flex-1 text-center font-heading font-semibold text-lg pr-10">{{ __('Manager') }}</span>
        </div>

        <!-- Main Content -->
        <main class="flex-1 overflow-auto">
            <div class="p-4 pt-18 md:p-8 md:pt-8">
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
        function switchManagerCompany(companyId) {
            fetch(`/manager/switch-company/${companyId}`, {
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
    @include('components.bottom-nav-manager')
    <x-live-chat-widget />
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
    <script>
        // Touch swipe to open/close sidebar
        let touchStartX = 0;
        let touchEndX = 0;
        document.addEventListener('touchstart', (e) => { touchStartX = e.changedTouches[0].screenX; }, { passive: true });
        document.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            const diff = touchEndX - touchStartX;
            if (diff > 80 && touchStartX < 30) {
                document.querySelector('[x-data]').__x.$data.sidebarOpen = true;
            } else if (diff < -80) {
                document.querySelector('[x-data]').__x.$data.sidebarOpen = false;
            }
        }, { passive: true });
    </script>
</body>
</html>
