@extends('layouts.app')

@section('title', __('Marketplace Services'))

@section('content')
<!-- Hero -->
<section class="section bg-gradient-to-br from-brand/10 to-bg-soft">
    <div class="container-risment">
        <div class="max-w-3xl mx-auto text-center">
            <h1 class="text-2xl sm:text-h1 font-heading mb-4">Услуги по маркетплейсам</h1>
            <p class="text-body-l text-text-muted">
                Полный цикл работы с маркетплейсами: от запуска до масштабирования продаж
            </p>
        </div>
    </div>
</section>

<!-- Launch Packages -->
<section class="section">
    <div class="container-risment">
        <div class="max-w-5xl mx-auto">
        <h2 class="text-h2 font-heading mb-8 text-center">Запуск аккаунта</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            @foreach($services['launch']->where('sku_limit', '>', 0) as $package)
            <div class="card hover:shadow-lg transition">
                <div class="text-h3 font-heading mb-2">{{ $package->getName() }}</div>
                <div class="text-body-s text-text-muted mb-4">{{ $package->getDescription() }}</div>
                
                <div class="mb-4">
                    <div class="text-h2 text-brand">{{ number_format($package->price, 0, '', ' ') }} сум</div>
                    <div class="text-body-s text-text-muted">{{ $package->getUnit() }}</div>
                </div>
                
                <div class="p-4 bg-bg-soft rounded-btn">
                    <div class="text-body-s text-text-muted mb-1">Включено</div>
                    <div class="font-semibold">до {{ $package->sku_limit }} SKU</div>
                </div>
            </div>
            @endforeach
        </div>
        
        @php $overageSku = $services['launch']->where('code', 'LAUNCH_OVER_SKU')->first(); @endphp
        @if($overageSku)
        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-btn text-center">
            <span class="font-semibold">{{ $overageSku->getName() }}:</span>
            {{ number_format($overageSku->price, 0, '', ' ') }} сум {{ $overageSku->getUnit() }}
        </div>
        @endif
        </div>
    </div>
</section>

<!-- Management Pricing Table with Toggle -->
<section class="section bg-bg-soft">
    <div class="container-risment">
        <h2 class="text-h2 font-heading text-center mb-3">Ежемесячное управление</h2>
        <p class="text-body-m text-text-muted text-center mb-8">
            Выберите тип маркетплейса для просмотра цен
        </p>
        
        <x-marketplace-pricing-table
            :uzum-packages="$uzumPackages"
            :complex-packages="$complexPackages"
        />
    </div>
</section>

<!-- Showcase Savings -->
<section class="section bg-gradient-to-br from-success/5 to-bg-soft">
    <div class="container-risment">
        <div class="max-w-4xl mx-auto">
            <x-marketplace-showcase-savings />
        </div>
    </div>
</section>

<!-- Bundle Discounts -->
<section class="section bg-gradient-to-br from-success/10 to-bg-soft">
    <div class="container-risment">
        <div class="max-w-5xl mx-auto">
        <div class="text-center mb-8">
            <h2 class="text-h2 font-heading mb-3">Скидки при подключении нескольких маркетплейсов</h2>
            <p class="text-body-m text-text-muted">
                Управляйте несколькими площадками одновременно и экономьте на ежемесячной подписке
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- 2 marketplaces -->
            <div class="card bg-white text-center hover:shadow-lg transition">
                <div class="text-h1 text-success mb-3">−7%</div>
                <div class="text-h4 font-heading mb-2">2 площадки</div>
                <p class="text-body-s text-text-muted">на ежемесячную абонплату за управление</p>
            </div>
            
            <!-- 3 marketplaces -->
            <div class="card bg-white text-center hover:shadow-lg transition">
                <div class="text-h1 text-success mb-3">−12%</div>
                <div class="text-h4 font-heading mb-2">3 площадки</div>
                <p class="text-body-s text-text-muted">на ежемесячную абонплату за управление</p>
            </div>
            
            <!-- 4 marketplaces - highlighted -->
            <div class="card bg-white border-2 border-success text-center hover:shadow-xl transition relative overflow-hidden">
                <div class="absolute top-2 right-2 px-3 py-1 bg-success text-white text-body-xs rounded-full">
                    МАКС
                </div>
                <div class="text-h1 text-success mb-3">−18%</div>
                <div class="text-h4 font-heading mb-2">Все 4 маркетплейса</div>
                <p class="text-body-s text-text-muted">максимальная экономия</p>
            </div>
        </div>
        
        <div class="p-6 bg-white rounded-btn border-2 border-success/20">
            <div class="flex flex-col sm:flex-row items-start gap-3 sm:gap-4">
                <div class="w-12 h-12 bg-success/10 rounded-btn flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-success" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <div class="font-semibold mb-2 text-success">Скидка применяется только к абонплате</div>
                    <ul class="space-y-1 text-body-s text-text-muted">
                        <li>✓ Скидка действует на ежемесячную подписку за управление</li>
                        <li>✗ Запуск аккаунта (одноразовый платеж) — без скидки</li>
                        <li>✗ Переплаты за SKU — без скидки</li>
                        <li>✗ Инфографика — без скидки</li>
                    </ul>
                </div>
            </div>
        </div>
        </div>
    </div>
</section>

<!-- Ads Add-on -->
@if($services['ads_addon'])
<section class="section">
    <div class="container-risment">
        <div class="max-w-4xl mx-auto">
        <div class="card bg-gradient-to-br from-warning/10 to-bg-soft border-2 border-warning/20">
            <div class="flex flex-col sm:flex-row items-start gap-4 sm:gap-6">
                <div class="w-16 h-16 bg-warning/10 rounded-btn flex items-center justify-center flex-shrink-0">
                    <svg class="w-8 h-8 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-h3 font-heading mb-2">{{ $services['ads_addon']->getName() }}</h3>
                    <p class="text-body-m text-text-muted mb-4">{{ $services['ads_addon']->getDescription() }}</p>
                    
                    <div class="flex items-baseline gap-2 mb-4">
                        <span class="text-h2 text-brand">{{ number_format($services['ads_addon']->price, 0, '', ' ') }} сум</span>
                        <span class="text-body-m text-text-muted">{{ $services['ads_addon']->getUnit() }}</span>
                    </div>
                    
                    <div class="p-3 bg-white rounded-btn text-body-s text-text-muted">
                        ⚠️ Рекламный бюджет оплачивается клиентом отдельно
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
</section>
@endif

<!-- Infographics -->
<section class="section bg-bg-soft">
    <div class="container-risment">
        <div class="max-w-4xl mx-auto">
        <h2 class="text-h2 font-heading mb-6 text-center">Инфографика</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            @foreach($services['infographics'] as $service)
            <div class="card">
                <div class="text-h4 font-heading mb-2">{{ $service->getName() }}</div>
                <p class="text-body-s text-text-muted mb-4">{{ $service->getDescription() }}</p>
                
                <div class="flex items-baseline gap-2">
                    <span class="text-h3 text-brand">{{ number_format($service->price, 0, '', ' ') }} сум</span>
                    <span class="text-body-s text-text-muted">{{ $service->getUnit() }}</span>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="p-4 bg-white border border-brand-border rounded-btn text-center text-body-s text-text-muted">
            📸 Клиент предоставляет фото и материалы для создания инфографики
        </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="section">
    <div class="container-risment">
        <div class="max-w-3xl mx-auto text-center">
        <h2 class="text-h2 font-heading mb-4">Готовы начать продавать?</h2>
        <p class="text-body-l text-text-muted mb-8">
            Свяжитесь с нами для подбора оптимального пакета услуг
        </p>
        <a href="{{ route('contacts', ['locale' => app()->getLocale()]) }}" class="btn btn-primary px-12">
            Связаться с нами
        </a>
        </div>
    </div>
</section>
@endsection
