{{-- Bottom Navigation for PWA standalone mode --}}
<div class="pwa-bottom-nav" x-data="{ moreOpen: false }">
    <nav class="fixed bottom-0 left-0 right-0 z-50 bg-white border-t border-gray-200 shadow-[0_-2px_10px_rgba(0,0,0,0.05)]" style="padding-bottom: env(safe-area-inset-bottom);">
        <div class="flex items-center justify-around h-14 max-w-lg mx-auto px-2">
            {{-- Dashboard --}}
            <a href="{{ route('cabinet.dashboard') }}" class="flex flex-col items-center justify-center gap-0.5 flex-1 py-1 {{ request()->routeIs('cabinet.dashboard') ? 'text-[#CB4FE4]' : 'text-gray-500' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1h-2z"/>
                </svg>
                <span class="text-[10px] font-medium leading-none">{{ __('Главная') }}</span>
            </a>

            {{-- Inventory --}}
            <a href="{{ route('cabinet.inventory.index') }}" class="flex flex-col items-center justify-center gap-0.5 flex-1 py-1 {{ request()->routeIs('cabinet.inventory.*') ? 'text-[#CB4FE4]' : 'text-gray-500' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <span class="text-[10px] font-medium leading-none">{{ __('Склад') }}</span>
            </a>

            {{-- Inbounds --}}
            <a href="{{ route('cabinet.inbounds.index') }}" class="flex flex-col items-center justify-center gap-0.5 flex-1 py-1 {{ request()->routeIs('cabinet.inbounds.*') ? 'text-[#CB4FE4]' : 'text-gray-500' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                </svg>
                <span class="text-[10px] font-medium leading-none">{{ __('Поставки') }}</span>
            </a>

            {{-- Shipments --}}
            <a href="{{ route('cabinet.shipments.index') }}" class="flex flex-col items-center justify-center gap-0.5 flex-1 py-1 {{ request()->routeIs('cabinet.shipments.*') ? 'text-[#CB4FE4]' : 'text-gray-500' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                </svg>
                <span class="text-[10px] font-medium leading-none">{{ __('Отгрузки') }}</span>
            </a>

            {{-- More --}}
            <button @click="moreOpen = !moreOpen" class="flex flex-col items-center justify-center gap-0.5 flex-1 py-1 text-gray-500 relative">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"/>
                </svg>
                <span class="text-[10px] font-medium leading-none">{{ __('Ещё') }}</span>

                {{-- More popup menu --}}
                <div x-show="moreOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-2" @click.outside="moreOpen = false" class="absolute bottom-full right-0 mb-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-2 overflow-hidden" style="display: none;">
                    <a href="{{ route('cabinet.tickets.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 {{ request()->routeIs('cabinet.tickets.*') ? 'text-[#CB4FE4] bg-purple-50' : '' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        {{ __('Тикеты') }}
                    </a>
                    <a href="{{ route('cabinet.finance.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 {{ request()->routeIs('cabinet.finance.*') ? 'text-[#CB4FE4] bg-purple-50' : '' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ __('Финансы') }}
                    </a>
                    <a href="{{ route('cabinet.billing.report') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 {{ request()->routeIs('cabinet.billing.*') ? 'text-[#CB4FE4] bg-purple-50' : '' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                        {{ __('Биллинг') }}
                    </a>
                    <a href="{{ route('cabinet.profile') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 {{ request()->routeIs('cabinet.profile') ? 'text-[#CB4FE4] bg-purple-50' : '' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        {{ __('Профиль') }}
                    </a>
                </div>
            </button>
        </div>
    </nav>
</div>
