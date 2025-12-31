@extends('layouts.app')

@section('title', __('Marketplace Calculator'))

@section('content')
<!-- Hero -->
<section class="section bg-gradient-to-br from-brand/10 to-bg-soft">
    <div class="container-risment max-w-4xl">
        <h1 class="text-h1 font-heading mb-4 text-center">Калькулятор управления маркетплейсом</h1>
        <p class="text-body-l text-text-muted text-center">
            Рассчитайте стоимость ежемесячного управления вашим аккаунтом
        </p>
    </div>
</section>

<!-- Calculator Form -->
<section class="section">
    <div class="container-risment max-w-4xl">
        <form method="POST" action="{{ route('calculators.marketplace.calculate', ['locale' => app()->getLocale()]) }}" class="card" x-data="{ marketplaces: @js(old('marketplaces', $result['marketplaces'] ?? [])) }">
            @csrf
            
            <h2 class="text-h2 font-heading mb-6">Параметры расчёта</h2>
            
            <!-- Marketplace Selection (Multi-select) -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-3">
                    <label class="block text-body-m font-semibold">
                        Маркетплейсы (выберите 1-4)
                        <span class="text-error">*</span>
                    </label>
                    <button 
                        type="button" 
                        @click="marketplaces = ['uzum', 'wildberries', 'ozon', 'yandex']"
                        class="btn btn-sm bg-success/10 text-success hover:bg-success/20 border-success/30">
                        ⚡ Все 4 площадки
                    </button>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach([
                        'uzum' => 'Uzum',
                        'wildberries' => 'Wildberries',
                        'ozon' => 'Ozon',
                        'yandex' => 'Yandex'
                    ] as $value => $label)
                    <label class="relative cursor-pointer">
                        <input 
                            type="checkbox" 
                            name="marketplaces[]" 
                            :value="'{{ $value }}'" 
                            class="peer sr-only"
                            x-model="marketplaces">
                        <div class="card peer-checked:border-2 peer-checked:border-brand peer-checked:bg-brand/5 transition-all text-center">
                            <div class="font-semibold">{{ $label }}</div>
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('marketplaces')
                <p class="text-error text-body-s mt-1">{{ $message }}</p>
                @enderror
                <p class="text-body-s text-text-muted mt-2">
                    💡 При выборе нескольких маркетплейсов действуют скидки: 2=−7%, 3=−12%, 4=−18%
                </p>
            </div>
            
            <hr class="my-8 border-brand-border">
            
            <!-- Package Selection -->
            <div class="mb-6">
                <label class="block text-body-m font-semibold mb-3">Пакет управления</label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($managementPackages as $package)
                    <label class="relative cursor-pointer">
                        <input type="radio" name="package_code" value="{{ $package->code }}" class="peer sr-only" required
                            {{ old('package_code') === $package->code ? 'checked' : '' }}>
                        <div class="p-4 border-2 rounded-btn peer-checked:border-brand peer-checked:bg-brand/5 transition-all">
                            <div class="font-semibold mb-2">{{ $package->getName() }}</div>
                            <div class="text-h3 text-brand mb-1">{{ number_format($package->price, 0, '', ' ') }}</div>
                            <div class="text-body-s text-text-muted">за 1 маркетплейс/мес</div>
                            <div class="mt-3 p-2 bg-bg-soft rounded text-body-s">
                                До {{ $package->sku_limit }} SKU
                            </div>
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('package_code')
                <p class="text-error text-body-s mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- SKU Count -->
            <div class="mb-6">
                <label class="block text-body-m font-semibold mb-2">Количество товаров (SKU)</label>
                <input type="number" name="sku_count" class="input max-w-md" value="{{ old('sku_count', 100) }}" min="0" required>
                <p class="text-body-s text-text-muted mt-1">Сколько артикулов планируете размещать на маркетплейсе</p>
                @error('sku_count')
                <p class="text-error text-body-s mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Ads Add-on -->
            <div class="mb-8">
                <label class="flex items-start gap-3 cursor-pointer max-w-2xl">
                    <input type="checkbox" name="ads_addon" value="1" class="mt-1"
                        {{ old('ads_addon') ? 'checked' : '' }}>
                    <div>
                        <div class="font-semibold mb-1">Добавить управление рекламой (+690 000 сум/мес)</div>
                        <div class="text-body-s text-text-muted">
                            Настройка и ведение рекламных кампаний. Рекламный бюджет оплачивается отдельно.
                        </div>
                    </div>
                </label>
            </div>
            
            <div class="flex justify-center">
                <button type="submit" class="btn btn-primary px-12">Рассчитать</button>
            </div>
        </form>
        
        <!-- Results -->
        @if(isset($result))
        <div class="card bg-bg-soft mt-8">
            <h2 class="text-h2 font-heading mb-6 text-center">Расчёт стоимости</h2>
            
            <div class="bg-white rounded-btn p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <div class="text-body-s text-text-muted mb-1">Выбрано площадок</div>
                        <div class="font-semibold">{{ $result['marketplaces_count'] }}</div>
                        <div class="text-body-xs text-text-muted mt-1">
                            {{ implode(', ', $result['marketplace_labels']) }}
                        </div>
                    </div>
                    <div>
                        <div class="text-body-s text-text-muted mb-1">Пакет</div>
                        <div class="font-semibold">{{ $result['package']->getName() }}</div>
                    </div>
                </div>
                
                <div class="space-y-3 text-body-m">
                    <div class="flex justify-between text-text-muted">
                        <span>База ({{ number_format($result['base_per_marketplace'], 0, '', ' ') }} × {{ $result['marketplaces_count'] }}):</span>
                        <span>{{ number_format($result['base_sum'], 0, '', ' ') }} сум</span>
                    </div>
                    
                    @if($result['discount_percent'] > 0)
                    <div class="flex justify-between text-success font-semibold">
                        <span>Скидка за {{ $result['marketplaces_count'] }} площадки ({{ $result['discount_percent'] }}%):</span>
                        <span>−{{ number_format($result['discount_amount'], 0, '', ' ') }} сум</span>
                    </div>
                    @endif
                    
                    <div class="flex justify-between font-semibold text-brand">
                        <span>Абонплата в месяц:</span>
                        <span>{{ number_format($result['discounted_sum'], 0, '', ' ') }} сум</span>
                    </div>
                    
                    @if($result['overage_count'] > 0)
                    <div class="border-t border-brand-border pt-3 flex justify-between text-warning">
                        <span>Переплата за SKU ({{ $result['overage_count'] }} шт = {{ $result['overage_packs'] }} пакетов по 10):</span>
                        <span class="font-semibold">+{{ number_format($result['overage_fee'], 0, '', ' ') }} сум</span>
                    </div>
                    <div class="text-body-xs text-text-muted pl-4">
                        {{ number_format($result['overage_fee_per_marketplace'], 0, '', ' ') }} × {{ $result['marketplaces_count'] }} площадок
                    </div>
                    @endif
                    
                    @if($result['ads_addon'])
                    <div class="flex justify-between">
                        <span>Управление рекламой:</span>
                        <span class="font-semibold">+{{ number_format($result['ads_fee'], 0, '', ' ') }} сум</span>
                    </div>
                    @endif
                    
                    <div class="border-t-2 border-brand pt-4 flex justify-between items-center">
                        <span class="text-h4 font-heading">Итого в месяц:</span>
                        <span class="text-h2 text-brand">{{ number_format($result['total'], 0, '', ' ') }} сум</span>
                    </div>
                </div>
            </div>
            
            <div class="p-4 bg-white rounded-btn space-y-2 text-body-s text-text-muted">
                <p>📌 DBS и FBO доставка оплачивается отдельно согласно тарифам fulfillment</p>
                <p>📌 Рекламный бюджет оплачивается клиентом напрямую маркетплейсу</p>
                <p>📌 Инфографика: 60 000 сум за основную, 40 000 сум за копии</p>
                @if($result['overage_count'] > 0 && $result['marketplaces_count'] > 1)
                <p>⚠️ Указанное количество SKU применяется к каждой площадке (всего {{ $result['sku_count'] * $result['marketplaces_count'] }} SKU)</p>
                @endif
            </div>
        </div>
        @endif
        
        <!-- Info -->
        <div class="mt-8 text-center">
            <p class="text-body-m text-text-muted mb-4">
                Нужна помощь с выбором пакета или есть дополнительные вопросы?
            </p>
            <a href="{{ route('contacts', ['locale' => app()->getLocale()]) }}" class="btn btn-secondary">
                Связаться с нами
            </a>
        </div>
    </div>
</section>
@endsection
