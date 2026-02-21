<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Manager') - RISMENT</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-body antialiased bg-bg">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-white border-r border-brand-border flex-shrink-0 sticky top-0 h-screen overflow-y-auto">
            <div class="p-6 border-b border-brand-border">
                <h1 class="text-h3 font-heading gradient-brand bg-clip-text text-transparent">RISMENT</h1>
                <p class="text-body-s text-text-muted mt-1">Панель менеджера</p>
            </div>

            <!-- Company Selector -->
            @if(isset($managerCompany) && $managerCompany)
                @if(isset($managedCompanies) && $managedCompanies->count() > 1)
                <div class="p-4 border-b border-brand-border">
                    <label class="text-body-s font-semibold text-text-muted">Компания</label>
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
                    <span>Главная</span>
                </a>

                <a href="{{ route('manager.tasks.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-btn {{ request()->routeIs('manager.tasks.*') ? 'bg-brand text-white' : 'hover:bg-bg-soft' }} mt-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                    <span>Задачи</span>
                </a>

                @php $pendingCount = isset($managerCompany) ? \App\Models\ManagerTask::forCompany($managerCompany->id)->pending()->count() : 0; @endphp
                <a href="{{ route('manager.confirmations.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-btn {{ request()->routeIs('manager.confirmations.*') ? 'bg-brand text-white' : 'hover:bg-bg-soft' }} mt-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Подтверждения</span>
                    @if($pendingCount > 0)
                        <span class="ml-auto bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $pendingCount }}</span>
                    @endif
                </a>

                <a href="{{ route('manager.billing.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-btn {{ request()->routeIs('manager.billing.*') ? 'bg-brand text-white' : 'hover:bg-bg-soft' }} mt-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Биллинг</span>
                </a>
            </nav>

            <!-- Logout -->
            <div class="absolute bottom-0 w-64 p-4 border-t border-brand-border bg-white">
                <form method="POST" action="{{ route('logout', ['locale' => app()->getLocale()]) }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-btn hover:bg-bg-soft text-error">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <span>Выйти</span>
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
</body>
</html>
