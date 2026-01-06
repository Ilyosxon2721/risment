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
    <div class="container-risment max-w-6xl">
        <form method="POST" action="{{ route('calculators.marketplace.calculate', ['locale' => app()->getLocale()]) }}" 
              x-data="{
                  marketplaces: @js(old('marketplaces', isset($items) ? array_keys($items) : [])),
                  configs: {
                      uzum: { package: @js(old('configs.uzum.package', '')), sku_count: @js(old('configs.uzum.sku_count', 100)), ads: @js(old('configs.uzum.ads', false)) },
                      wildberries: { package: @js(old('configs.wildberries.package', '')), sku_count: @js(old('configs.wildberries.sku_count', 60)), ads: @js(old('configs.wildberries.ads', false)) },
                      ozon: { package: @js(old('configs.ozon.package', '')), sku_count: @js(old('configs.ozon.sku_count', 60)), ads: @js(old('configs.ozon.ads', false)) },
                      yandex: { package: @js(old('configs.yandex.package', '')), sku_count: @js(old('configs.yandex.sku_count', 60)), ads: @js(old('configs.yandex.ads', false)) }
                  },
                  get selectedCount() { return this.marketplaces.length; }
              }">
            @csrf
            
            <div class="card mb-8">
                <h2 class="text-h2 font-heading mb-6">Шаг 1: Выберите маркетплейсы</h2>
                
                <!-- Marketplace Selection -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-4">
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
                    <p class="text-error text-body-s mt-2">{{ $message }}</p>
                    @enderror
                    
                    <div class="mt-4 p-4 bg-success/5 border border-success/20 rounded-btn">
                        <p class="text-body-s text-success font-semibold mb-2">💰 Выгода при подключении нескольких площадок:</p>
                        <div class="grid grid-cols-3 gap-2 text-body-xs text-text-muted">
                            <div>2 площадки: <strong class="text-success">−7%</strong></div>
                            <div>3 площадки: <strong class="text-success">−12%</strong></div>
                            <div>4 площадки: <strong class="text-success">−18%</strong></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Per-Marketplace Configuration Cards -->
            <div class="space-y-6 mb-8" x-show="selectedCount > 0" x-cloak>
                <h2 class="text-h2 font-heading">Шаг 2: Настройте каждую площадку</h2>
                
                <!-- Debug info (remove after testing) -->
                <div class="p-3 bg-yellow-50 border border-yellow-200 rounded text-xs">
                    Debug: Выбрано <span x-text="selectedCount"></span> площадок. 
                    Uzum пакетов: {{ $uzumPackages->count() }}, Complex пакетов: {{ $complexPackages->count() }}
                </div>
                
                <!-- Uzum Card -->
                <div x-show="marketplaces.includes('uzum')" x-cloak class="card border-l-4 border-l-brand">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-brand/10 rounded-btn flex items-center justify-center">
                            <span class="text-2xl">🛒</span>
                        </div>
                        <h3 class="text-h3 font-heading">Uzum Market</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-body-m font-semibold mb-2">Пакет управления</label>
                            <select name="configs[uzum][package]" x-model="configs.uzum.package" class="input">
                                <option value="">Выберите пакет</option>
                                @foreach($uzumPackages as $pkg)
                                <option value="{{ $pkg->code }}">{{ $pkg->getShortName() }} - {{ number_format($pkg->price, 0, '', ' ') }} сум/мес</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-body-m font-semibold mb-2">Количество SKU</label>
                            <input type="number" name="configs[uzum][sku_count]" x-model="configs.uzum.sku_count" class="input" min="0">
                        </div>
                        
                        <div class="flex items-end">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="configs[uzum][ads]" value="1" x-model="configs.uzum.ads" class="w-5 h-5">
                                <div>
                                    <div class="font-semibold text-body-m">Управление рекламой</div>
                                    <div class="text-body-xs text-text-muted">+690 000 сум/мес</div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                
                <!-- Wildberries Card -->
                <div x-show="marketplaces.includes('wildberries')" x-cloak class="card border-l-4 border-l-purple-500">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-purple-500/10 rounded-btn flex items-center justify-center">
                            <span class="text-2xl">🟣</span>
                        </div>
                        <h3 class="text-h3 font-heading">Wildberries</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-body-m font-semibold mb-2">Пакет управления</label>
                            <select name="configs[wildberries][package]" x-model="configs.wildberries.package" class="input">
                                <option value="">Выберите пакет</option>
                                @foreach($complexPackages as $pkg)
                                <option value="{{ $pkg->code }}">{{ $pkg->getShortName() }} - {{ number_format($pkg->price, 0, '', ' ') }} сум/мес</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-body-m font-semibold mb-2">Количество SKU</label>
                            <input type="number" name="configs[wildberries][sku_count]" x-model="configs.wildberries.sku_count" class="input" min="0">
                        </div>
                        
                        <div class="flex items-end">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="configs[wildberries][ads]" value="1" x-model="configs.wildberries.ads" class="w-5 h-5">
                                <div>
                                    <div class="font-semibold text-body-m">Управление рекламой</div>
                                    <div class="text-body-xs text-text-muted">+690 000 сум/мес</div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                
                <!-- Ozon Card -->
                <div x-show="marketplaces.includes('ozon')" x-cloak class="card border-l-4 border-l-blue-500">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-blue-500/10 rounded-btn flex items-center justify-center">
                            <span class="text-2xl">🔵</span>
                        </div>
                        <h3 class="text-h3 font-heading">Ozon</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-body-m font-semibold mb-2">Пакет управления</label>
                            <select name="configs[ozon][package]" x-model="configs.ozon.package" class="input">
                                <option value="">Выберите пакет</option>
                                @foreach($complexPackages as $pkg)
                                <option value="{{ $pkg->code }}">{{ $pkg->getShortName() }} - {{ number_format($pkg->price, 0, '', ' ') }} сум/мес</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-body-m font-semibold mb-2">Количество SKU</label>
                            <input type="number" name="configs[ozon][sku_count]" x-model="configs.ozon.sku_count" class="input" min="0">
                        </div>
                        
                        <div class="flex items-end">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="configs[ozon][ads]" value="1" x-model="configs.ozon.ads" class="w-5 h-5">
                                <div>
                                    <div class="font-semibold text-body-m">Управление рекламой</div>
                                    <div class="text-body-xs text-text-muted">+690 000 сум/мес</div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                
                <!-- Yandex Card -->
                <div x-show="marketplaces.includes('yandex')" x-cloak class="card border-l-4 border-l-red-500">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-red-500/10 rounded-btn flex items-center justify-center">
                            <span class="text-2xl">🔴</span>
                        </div>
                        <h3 class="text-h3 font-heading">Yandex Market</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-body-m font-semibold mb-2">Пакет управления</label>
                            <select name="configs[yandex][package]" x-model="configs.yandex.package" class="input">
                                <option value="">Выберите пакет</option>
                                @foreach($complexPackages as $pkg)
                                <option value="{{ $pkg->code }}">{{ $pkg->getShortName() }} - {{ number_format($pkg->price, 0, '', ' ') }} сум/мес</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-body-m font-semibold mb-2">Количество SKU</label>
                            <input type="number" name="configs[yandex][sku_count]" x-model="configs.yandex.sku_count" class="input" min="0">
                        </div>
                        
                        <div class="flex items-end">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="configs[yandex][ads]" value="1" x-model="configs.yandex.ads" class="w-5 h-5">
                                <div>
                                    <div class="font-semibold text-body-m">Управление рекламой</div>
                                    <div class="text-body-xs text-text-muted">+690 000 сум/мес</div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-center">
                <button type="submit" class="btn btn-primary px-12" x-bind:disabled="selectedCount === 0">
                    Рассчитать стоимость
                </button>
            </div>
        </form>
        
        <!-- Results -->
        @if(isset($items))
        <div class="card bg-bg-soft mt-8">
            <h2 class="text-h2 font-heading mb-6 text-center">Расчёт стоимости</h2>
            
            <div class="bg-white rounded-btn p-6 mb-6">
                <!-- Detailed Items -->
                <div class="space-y-6 mb-8">
                    @foreach($items as $mp => $data)
                    <div class="border-b border-gray-100 pb-4 last:border-0 last:pb-0">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h4 class="font-bold text-lg text-brand">{{ $data['name'] }}</h4>
                                <div class="text-body-s text-text-muted">
                                    {{ $data['package']->getName() }} ({{ $data['sku_count'] }} SKU)
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold">{{ number_format($data['total'], 0, '', ' ') }} сум</div>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2 text-body-xs text-text-muted">
                            <div class="flex justify-between md:block">
                                <span>База:</span>
                                <span class="md:block font-semibold">{{ number_format($data['base_price'], 0, '', ' ') }} сум</span>
                            </div>
                            @if($data['overage_fee'] > 0)
                            <div class="flex justify-between md:block text-warning">
                                <span>Переплата (SKU):</span>
                                <span class="md:block font-semibold">+{{ number_format($data['overage_fee'], 0, '', ' ') }} сум</span>
                            </div>
                            @endif
                            @if($data['ads_fee'] > 0)
                            <div class="flex justify-between md:block text-brand">
                                <span>Реклама:</span>
                                <span class="md:block font-semibold">+{{ number_format($data['ads_fee'], 0, '', ' ') }} сум</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>

                <hr class="mb-6">
                
                <div class="space-y-3 text-body-m">
                    <div class="flex justify-between text-text-muted">
                        <span>Сумма пакетов:</span>
                        <span>{{ number_format($base_sum, 0, '', ' ') }} сум</span>
                    </div>
                    
                    @if($discount_percent > 0)
                    <div class="flex justify-between text-success font-semibold">
                        <span>Скидка за {{ count($items) }} площадки ({{ $discount_percent }}%):</span>
                        <span>−{{ number_format($discount_amount, 0, '', ' ') }} сум</span>
                    </div>
                    @endif
                    
                    @if($total_overage > 0)
                    <div class="flex justify-between text-warning">
                        <span>Общая переплата за SKU:</span>
                        <span>+{{ number_format($total_overage, 0, '', ' ') }} сум</span>
                    </div>
                    @endif
                    
                    @if($total_ads > 0)
                    <div class="flex justify-between text-brand font-semibold">
                        <span>Управление рекламой (всего):</span>
                        <span>+{{ number_format($total_ads, 0, '', ' ') }} сум</span>
                    </div>
                    @endif
                    
                    <div class="border-t-2 border-brand pt-4 flex justify-between items-center">
                        <span class="text-h4 font-heading">Итого в месяц:</span>
                        <span class="text-h2 text-brand">{{ number_format($total, 0, '', ' ') }} сум</span>
                    </div>
                </div>
            </div>
            
            <div class="p-4 bg-white rounded-btn space-y-2 text-body-s text-text-muted">
                <p>📌 DBS и FBO доставка оплачивается отдельно согласно тарифам fulfillment</p>
                <p>📌 Рекламный бюджет оплачивается клиентом напрямую маркетплейсу</p>
                <p>📌 Инфографика: 60 000 сум за основную, 40 000 сум за копии</p>
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

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
