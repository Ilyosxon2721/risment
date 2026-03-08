{{-- Bottom Navigation for PWA standalone mode (Manager) --}}
<div class="pwa-bottom-nav" x-data="{ moreOpen: false }">
    <nav class="fixed bottom-0 left-0 right-0 z-50 bg-white border-t border-gray-200 shadow-[0_-2px_10px_rgba(0,0,0,0.05)]" style="padding-bottom: env(safe-area-inset-bottom);">
        <div class="flex items-center justify-around h-14 max-w-lg mx-auto px-2">
            {{-- Dashboard --}}
            <a href="{{ route('manager.dashboard') }}" class="flex flex-col items-center justify-center gap-0.5 flex-1 py-1 {{ request()->routeIs('manager.dashboard') ? 'text-[#CB4FE4]' : 'text-gray-500' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1h-2z"/>
                </svg>
                <span class="text-[10px] font-medium leading-none">{{ __('Главная') }}</span>
            </a>

            {{-- Tasks --}}
            <a href="{{ route('manager.tasks.index') }}" class="flex flex-col items-center justify-center gap-0.5 flex-1 py-1 relative {{ request()->routeIs('manager.tasks.*') ? 'text-[#CB4FE4]' : 'text-gray-500' }}">
                <div class="relative">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    @php $tasksPending = isset($managerCompany) ? \App\Models\ManagerTask::forCompany($managerCompany->id)->where('status', 'pending')->count() : 0; @endphp
                    @if($tasksPending > 0)
                        <span class="absolute -top-1.5 -right-2.5 bg-red-500 text-white text-[9px] font-bold min-w-[16px] h-4 flex items-center justify-center rounded-full px-1">{{ $tasksPending }}</span>
                    @endif
                </div>
                <span class="text-[10px] font-medium leading-none">{{ __('Задачи') }}</span>
            </a>

            {{-- Confirmations --}}
            <a href="{{ route('manager.confirmations.index') }}" class="flex flex-col items-center justify-center gap-0.5 flex-1 py-1 relative {{ request()->routeIs('manager.confirmations.*') ? 'text-[#CB4FE4]' : 'text-gray-500' }}">
                <div class="relative">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    @php $pendingCount = isset($managerCompany) ? \App\Models\ManagerTask::forCompany($managerCompany->id)->pending()->count() : 0; @endphp
                    @if($pendingCount > 0)
                        <span class="absolute -top-1.5 -right-2.5 bg-red-500 text-white text-[9px] font-bold min-w-[16px] h-4 flex items-center justify-center rounded-full px-1">{{ $pendingCount }}</span>
                    @endif
                </div>
                <span class="text-[10px] font-medium leading-none">{{ __('Подтверждения') }}</span>
            </a>

            {{-- Inventory --}}
            <a href="{{ route('manager.inventory.index') }}" class="flex flex-col items-center justify-center gap-0.5 flex-1 py-1 {{ request()->routeIs('manager.inventory.*') ? 'text-[#CB4FE4]' : 'text-gray-500' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <span class="text-[10px] font-medium leading-none">{{ __('Склад') }}</span>
            </a>

            {{-- More --}}
            <button @click="moreOpen = !moreOpen" class="flex flex-col items-center justify-center gap-0.5 flex-1 py-1 text-gray-500 relative">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"/>
                </svg>
                <span class="text-[10px] font-medium leading-none">{{ __('Ещё') }}</span>

                {{-- More popup menu --}}
                <div x-show="moreOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-2" @click.outside="moreOpen = false" class="absolute bottom-full right-0 mb-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-2 overflow-hidden" style="display: none;">
                    <a href="{{ route('manager.shipments.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 {{ request()->routeIs('manager.shipments.*') ? 'text-[#CB4FE4] bg-purple-50' : '' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                        {{ __('Отгрузки') }}
                    </a>
                    <a href="{{ route('manager.inbounds.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 {{ request()->routeIs('manager.inbounds.*') ? 'text-[#CB4FE4] bg-purple-50' : '' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                        {{ __('Поставки') }}
                    </a>
                    <a href="{{ route('manager.billing.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 {{ request()->routeIs('manager.billing.*') ? 'text-[#CB4FE4] bg-purple-50' : '' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                        {{ __('Биллинг') }}
                    </a>
                </div>
            </button>
        </div>
    </nav>
</div>
