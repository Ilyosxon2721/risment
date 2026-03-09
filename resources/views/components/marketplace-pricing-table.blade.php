@props(['uzumPackages', 'complexPackages'])

@php
$alpineData = [
    'marketplaceGroup' => 'uzum',
    'packages' => [
        'uzum' => $uzumPackages,
        'complex' => $complexPackages,
    ],
];
@endphp

<div x-data='@json($alpineData)' x-init="
    currentPackages = () => packages[marketplaceGroup]
">
    <!-- Toggle -->
    <div class="flex justify-center mb-6">
        <div class="inline-flex bg-bg-soft rounded-btn p-1 w-full sm:w-auto">
            <button
                @click="marketplaceGroup = 'uzum'"
                :class="marketplaceGroup === 'uzum' ? 'bg-brand text-white' : 'text-text-muted hover:text-text'"
                class="flex-1 sm:flex-none px-4 sm:px-8 py-3 rounded-btn transition font-semibold text-body-m min-h-[44px]">
                Uzum Market
            </button>
            <button
                @click="marketplaceGroup = 'complex'"
                :class="marketplaceGroup === 'complex' ? 'bg-brand text-white' : 'text-text-muted hover:text-text'"
                class="flex-1 sm:flex-none px-4 sm:px-8 py-3 rounded-btn transition font-semibold text-body-m min-h-[44px]">
                WB / Ozon / Yandex
            </button>
        </div>
    </div>
    
    <!-- Subtitle badge -->
    <p class="text-center text-body-s text-text-muted mb-8" x-show="marketplaceGroup === 'uzum'" x-transition>
        💡 Uzum проще, сложные площадки требуют больше регламентов
    </p>
    <p class="text-center text-body-s text-text-muted mb-8" x-show="marketplaceGroup === 'complex'" x-transition x-cloak>
        💡 Wildberries, Ozon, Yandex — больше требований к контенту и процессам
    </p>
    
    <!-- Desktop Table -->
    <div class="mx-auto max-w-4xl">
        <div class="hidden md:block overflow-x-auto bg-white rounded-card shadow-sm">
            <table class="w-full">
                <thead>
                    <tr class="border-b-2 border-brand-border">
                        <th class="text-left py-4 px-4 font-semibold">Пакет</th>
                        <th class="text-left py-4 px-4 font-semibold">Цена в месяц</th>
                        <th class="text-left py-4 px-4 font-semibold">Лимиты</th>
                        <th class="text-right py-4 px-4"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="pkg in currentPackages()" :key="pkg.code">
                        <tr class="border-b border-brand-border hover:bg-bg-soft transition">
                            <td class="py-5 px-4">
                                <div class="font-semibold text-h4" x-text="pkg.name_ru"></div>
                                <div class="text-body-s text-text-muted mt-1" x-text="pkg.description_ru"></div>
                            </td>
                            <td class="py-5 px-4">
                                <div class="text-h3 text-brand" x-text="Math.floor(pkg.price).toLocaleString('ru-RU') + ' сум'"></div>
                                <div class="text-body-s text-text-muted" x-text="pkg.unit_ru"></div>
                            </td>
                            <td class="py-5 px-4 text-body-s">
                                <template x-if="pkg.sku_limit">
                                    <div>
                                        <div>До <span x-text="pkg.sku_limit"></span> SKU</div>
                                        <div class="text-text-muted mt-1">+50 000 сум за каждые 10 SKU</div>
                                    </div>
                                </template>
                                <template x-if="!pkg.sku_limit">
                                    <div>
                                        <div class="font-semibold text-warning">Индивидуально</div>
                                        <div class="text-text-muted mt-1">Обсуждается отдельно</div>
                                    </div>
                                </template>
                            </td>
                            <td class="py-5 px-4 text-right">
                                <a :href="'{{ route('calculators.marketplace', app()->getLocale()) }}'" class="btn btn-sm btn-secondary whitespace-nowrap">
                                    Рассчитать
                                </a>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Mobile Cards -->
    <div class="md:hidden space-y-4">
        <template x-for="pkg in currentPackages()" :key="pkg.code">
            <div class="card">
                <div class="font-semibold text-h4 mb-2" x-text="pkg.name_ru"></div>
                <div class="text-body-s text-text-muted mb-3" x-text="pkg.description_ru"></div>
                
                <div class="mb-4">
                    <div class="text-h3 text-brand" x-text="Math.floor(pkg.price).toLocaleString('ru-RU') + ' сум'"></div>
                    <div class="text-body-s text-text-muted" x-text="pkg.unit_ru"></div>
                </div>
                
                <div class="text-body-s mb-4">
                    <template x-if="pkg.sku_limit">
                        <div>
                            <div>До <span x-text="pkg.sku_limit"></span> SKU</div>
                            <div class="text-text-muted">+50 000 сум за каждые 10 SKU</div>
                        </div>
                    </template>
                    <template x-if="!pkg.sku_limit">
                        <div>
                            <div class="font-semibold text-warning">Индивидуально</div>
                            <div class="text-text-muted">Обсуждается отдельно</div>
                        </div>
                    </template>
                </div>
                
                <a :href="'{{ route('calculators.marketplace', app()->getLocale()) }}'" class="btn btn-secondary w-full min-h-[44px]">
                    Рассчитать
                </a>
            </div>
        </template>
    </div>
    
    <!-- Notes -->
    <div class="mt-8 p-6 bg-bg-soft rounded-btn text-body-s text-text-muted space-y-2">
        <p>📌 Рекламный бюджет оплачивается клиентом отдельно</p>
        <p>📌 Инфографика: 60 000 первая, 40 000 копия</p>
        <p>📌 Скидки применяются только к абонплате за управление</p>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
