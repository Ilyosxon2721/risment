@extends('layouts.app')

@section('title', __('Calculator') . ' - RISMENT')

@section('content')
@php
    use App\Models\ContentBlock;
    $explanation = ContentBlock::getBlock('calculator', 'explanation');
@endphp

<section class="py-16">
    <div class="container-risment max-w-4xl">
        <h1 class="text-h1 font-heading text-center mb-4">{{ __('Calculator') }}</h1>
        
        @if($explanation)
        <p class="text-body-l text-text-muted text-center mb-12">{{ $explanation->getBody() }}</p>
        @endif
        
        {{-- Input Form --}}
        <form method="POST" action="{{ route('calculator.calculate', ['locale' => app()->getLocale()]) }}" class="card mb-8">
            @csrf
            
            {{-- FBS Delivery Size Breakdown --}}
            <div class="mb-6">
                <h3 class="text-h3 font-heading mb-2">{{ __('Логистика FBS (по размерам)') }}</h3>
                <p class="text-body-s text-text-muted mb-4">{{ __('Укажите количество отправок по каждому размеру') }}</p>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-body-m font-semibold mb-2">
                            MGT (≤60 см)
                            <span class="text-brand ml-2">11 000 {{ __('сум/шт') }}</span>
                        </label>
                        <input type="number" name="mgt_count" class="input" value="{{ old('mgt_count', $result['usage']['mgt_count'] ?? 0) }}" min="0" required>
                        <p class="text-body-s text-text-muted mt-1">{{ __('Количество малых посылок') }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-body-m font-semibold mb-2">
                            SGT (61-120 см)
                            <span class="text-brand ml-2">15 000 {{ __('сум/шт') }}</span>
                        </label>
                        <input type="number" name="sgt_count" class="input" value="{{ old('sgt_count', $result['usage']['sgt_count'] ?? 0) }}" min="0" required>
                        <p class="text-body-s text-text-muted mt-1">{{ __('Количество средних посылок') }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-body-m font-semibold mb-2">
                            KGT (>120 см)
                            <span class="text-brand ml-2">27 000 {{ __('сум/шт') }}</span>
                        </label>
                        <input type="number" name="kgt_count" class="input" value="{{ old('kgt_count', $result['usage']['kgt_count'] ?? 0) }}" min="0" required>
                        <p class="text-body-s text-text-muted mt-1">{{ __('Количество крупных посылок') }}</p>
                    </div>
                </div>
                
                <p class="text-body-s text-text-muted mt-3">
                    {{ __('L+W+H = длина + ширина + высота в см') }}
                </p>
            </div>
            
            <hr class="my-8 border-brand-border">
            
            {{-- Expected Usage --}}
            <div class="mb-6">
                <h3 class="text-h3 font-heading mb-4">{{ __('Ожидаемые объёмы в месяц') }}</h3>
                <p class="text-body-s text-text-muted mb-4">{{ __('Укажите ожидаемое использование хранения и приёмки') }}</p>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-body-m font-semibold mb-2">{{ __('Коробов на хранении') }}</label>
                        <input type="number" name="storage_boxes" class="input" value="{{ old('storage_boxes', $result['usage']['storage_boxes'] ?? 0) }}" min="0" required>
                        <p class="text-body-s text-text-muted mt-1">{{ __('Средний объём 60×40×40') }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-body-m font-semibold mb-2">{{ __('Мешков на хранении') }}</label>
                        <input type="number" name="storage_bags" class="input" value="{{ old('storage_bags', $result['usage']['storage_bags'] ?? 0) }}" min="0" required>
                        <p class="text-body-s text-text-muted mt-1">{{ __('Средний объём мешков одежды') }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-body-m font-semibold mb-2">{{ __('Коробов приёмки') }}</label>
                        <input type="number" name="inbound_boxes" class="input" value="{{ old('inbound_boxes', $result['usage']['inbound_boxes'] ?? 0) }}" min="0" required>
                        <p class="text-body-s text-text-muted mt-1">{{ __('Поставки в месяц') }}</p>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-center mt-6">
                <button type="submit" class="btn btn-primary px-12">{{ __('Рассчитать стоимость') }}</button>
            </div>
        </form>
        
        {{-- Results Section --}}
        @if(isset($result))
        
        {{-- Step 1: Summary of Input --}}
        <div class="card bg-blue-50 border border-blue-200 mb-6">
            <div class="flex items-start gap-3">
                <div class="text-3xl">📊</div>
                <div class="flex-1">
                    <h3 class="text-h4 font-heading mb-2">{{ __('Ваши объемы в месяц') }}</h3>
                    <div class="space-y-1 text-body-m">
                        <p><strong>{{ $result['usage']['total_shipments'] }} {{ __('FBS отправлений') }}</strong> 
                            @if($result['usage']['mgt_count'] > 0)
                                ({{ $result['usage']['mgt_count'] }} MGT
                            @endif
                            @if($result['usage']['sgt_count'] > 0)
                                @if($result['usage']['mgt_count'] > 0), @endif {{ $result['usage']['sgt_count'] }} SGT
                            @endif
                            @if($result['usage']['kgt_count'] > 0)
                                @if($result['usage']['mgt_count'] > 0 || $result['usage']['sgt_count'] > 0), @endif {{ $result['usage']['kgt_count'] }} KGT
                            @endif)
                        </p>
                        @if($result['usage']['storage_boxes'] > 0 || $result['usage']['storage_bags'] > 0)
                        <p><strong>{{ __('Хранение:') }}</strong> {{ $result['usage']['storage_boxes'] }} {{ __('коробов') }} + {{ $result['usage']['storage_bags'] }} {{ __('мешков') }}</p>
                        @endif
                        @if($result['usage']['inbound_boxes'] > 0)
                        <p><strong>{{ __('Приёмка:') }}</strong> {{ $result['usage']['inbound_boxes'] }} {{ __('коробов') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Step 2: Main Recommendation --}}
        @php
            $recommended = $result['comparison']['recommended'];
            $isPackage = $recommended['type'] === 'plan';
            $perUnitOption = collect($result['comparison']['all_options'])->where('type', 'per_unit')->first();
        @endphp
        
        <div class="card bg-gradient-to-br from-green-50 to-emerald-50 border-2 border-green-500 mb-6">
            <div class="text-center mb-4">
                <div class="inline-block px-4 py-2 bg-green-500 text-white rounded-full text-body-m font-semibold mb-3">
                    ✨ {{ __('НАША РЕКОМЕНДАЦИЯ') }}
                </div>
            </div>
            
            @if($isPackage)
            <div class="text-center mb-6">
                <h2 class="text-h1 font-heading text-brand mb-2">{{ __('Пакет') }} {{ $recommended['plan']->getName() }}</h2>
                <div class="text-5xl font-bold text-green-600 mb-2">
                    {{ number_format($recommended['total'], 0, '', ' ') }} <span class="text-2xl">{{ __('сум/мес') }}</span>
                </div>
                
                @if($recommended['savings_vs_per_unit'] > 0)
                <div class="inline-block px-4 py-2 bg-white rounded-btn shadow-sm">
                    <span class="text-green-600 font-semibold text-h4">💰 {{ __('Экономия') }} -{{ number_format($recommended['savings_vs_per_unit'], 0, '', ' ') }} {{ __('сум/мес') }}</span>
                    <span class="text-text-muted text-body-m">({{ number_format($recommended['savings_percent'], 1) }}%)</span>
                </div>
                @endif
            </div>
            
            <div class="bg-white rounded-btn p-6 mb-6">
                <h4 class="font-semibold text-body-l mb-3">{{ __('Почему этот пакет вам подходит:') }}</h4>
                <ul class="space-y-2">
                    @if($result['usage']['total_shipments'] <= $recommended['plan']->fbs_shipments_included)
                    <li class="flex items-start gap-2">
                        <span class="text-green-500 text-xl">✅</span>
                        <span>{{ __('Ваш объем') }} ({{ $result['usage']['total_shipments'] }} шт) {{ __('вписывается в лимит пакета') }} ({{ $recommended['plan']->fbs_shipments_included }} шт)</span>
                    </li>
                    @else
                    <li class="flex items-start gap-2">
                        <span class="text-yellow-500 text-xl">⚠️</span>
                        <span>{{ __('Небольшое превышение лимита') }}: +{{ $result['usage']['total_shipments'] - $recommended['plan']->fbs_shipments_included }} шт ({{ number_format($recommended['breakdown']['overage']['total'], 0, '', ' ') }} сум)</span>
                    </li>
                    @endif
                    
                    @if($recommended['savings_vs_per_unit'] > 0)
                    <li class="flex items-start gap-2">
                        <span class="text-green-500 text-xl">✅</span>
                        <span>{{ __('Экономия на подписке — нет надбавки за разовую оплату') }}</span>
                    </li>
                    @endif
                    
                    @if($recommended['plan']->priority_processing)
                    <li class="flex items-start gap-2">
                        <span class="text-green-500 text-xl">✅</span>
                        <span>{{ __('Приоритетная обработка заказов') }}</span>
                    </li>
                    @endif
                    
                    @if($recommended['plan']->personal_manager)
                    <li class="flex items-start gap-2">
                        <span class="text-green-500 text-xl">✅</span>
                        <span>{{ __('Персональный менеджер') }}</span>
                    </li>
                    @endif
                </ul>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('cabinet.subscription.choose', ['locale' => app()->getLocale()]) }}" class="btn btn-primary btn-lg">
                    ✅ {{ __('Выбрать пакет') }} {{ $recommended['plan']->getName() }}
                </a>
                <button onclick="document.getElementById('package-details').scrollIntoView({behavior: 'smooth'})" class="btn btn-secondary">
                    📋 {{ __('Показать детали') }}
                </button>
            </div>
            
            @else
            {{-- If per-unit is recommended --}}
            <div class="text-center mb-6">
                <h2 class="text-h1 font-heading text-brand mb-2">{{ __('Оплата по факту') }}</h2>
                <div class="text-5xl font-bold text-green-600 mb-2">
                    {{ number_format($recommended['total'], 0, '', ' ') }} <span class="text-2xl">{{ __('сум/мес') }}</span>
                </div>
                <p class="text-body-l text-text-muted">{{ __('Без абонплаты, оплата только за использованные услуги') }}</p>
            </div>
            
            <div class="bg-white rounded-btn p-6 mb-6">
                <h4 class="font-semibold text-body-l mb-3">{{ __('Почему оплата по факту:') }}</h4>
                <ul class="space-y-2">
                    <li class="flex items-start gap-2">
                        <span class="text-green-500 text-xl">✅</span>
                        <span>{{ __('Небольшой объем — пакет пока нецелесообразен') }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-blue-500 text-xl">💡</span>
                        <span>{{ __('При росте до') }} {{ collect($result['comparison']['all_options'])->where('type', 'plan')->first()['plan']->fbs_shipments_included }} {{ __('отправлений рекомендуем перейти на пакет') }}</span>
                    </li>
                </ul>
            </div>
            
            <div class="flex justify-center">
                <a href="{{ route('contacts', ['locale' => app()->getLocale()]) }}" class="btn btn-primary btn-lg">
                    💬 {{ __('Связаться с нами') }}
                </a>
            </div>
            @endif
        </div>
        
        {{-- Step 3: Comparison Table (Collapsible) --}}
        <details class="card mb-6" id="comparison-section">
            <summary class="cursor-pointer font-heading text-h4 flex items-center gap-2 hover:text-brand transition">
                <span>🔍</span>
                <span>{{ __('Сравнить все варианты') }}</span>
                <svg class="w-5 h-5 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </summary>
            
            <div class="mt-6 overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-bg-soft">
                        <tr>
                            <th class="px-4 py-3 text-left">{{ __('Вариант') }}</th>
                            <th class="px-4 py-3 text-right">{{ __('Стоимость') }}</th>
                            <th class="px-4 py-3 text-left">{{ __('Особенности') }}</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($result['comparison']['all_options'] as $option)
                        <tr class="border-t border-brand-border {{ $option['type'] === $recommended['type'] && ($option['type'] === 'per_unit' || $option['plan']->code === $recommended['plan']->code) ? 'bg-green-50' : '' }}">
                            <td class="px-4 py-4">
                                <div class="font-semibold">
                                    @if($option['type'] === 'plan')
                                        {{ $option['plan']->getName() }}
                                        @if($option['type'] === $recommended['type'] && $option['plan']->code === $recommended['plan']->code)
                                            <span class="ml-2 text-green-600">⭐</span>
                                        @endif
                                    @else
                                        {{ __('Без пакета') }}
                                        @if($option['type'] === $recommended['type'])
                                            <span class="ml-2 text-green-600">⭐</span>
                                        @endif
                                    @endif
                                </div>
                                @if($option['type'] === 'plan')
                                    <div class="text-body-s text-text-muted">{{ __('Абонплата') }}: {{ number_format($option['breakdown']['monthly_fee'], 0, '', ' ') }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-right font-semibold text-brand">
                                {{ number_format($option['total'], 0, '', ' ') }} <span class="text-body-s">{{ __('сум') }}</span>
                            </td>
                            <td class="px-4 py-4">
                                @if($option['type'] === 'plan')
                                    @if($option['breakdown']['overage']['total'] > 0)
                                        <span class="text-warning text-body-s">+{{ number_format($option['breakdown']['overage']['total'], 0, '', ' ') }} {{ __('за превышение') }}</span>
                                    @else
                                        <span class="text-success text-body-s">✅ {{ __('В пределах лимита') }}</span>
                                    @endif
                                @else
                                    @if($option['breakdown']['surcharge_percent'] > 0)
                                        <span class="text-warning text-body-s">⚠️ +{{ number_format($option['breakdown']['surcharge_percent'], 0) }}% {{ __('надбавка') }}</span>
                                    @endif
                                @endif
                            </td>
                            <td class="px-4 py-4 text-right">
                                @if($option['type'] === 'plan')
                                    <a href="{{ route('cabinet.subscription.choose', ['locale' => app()->getLocale()]) }}" class="btn btn-secondary btn-sm">
                                        {{ __('Выбрать') }}
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-btn text-body-s">
                <p class="text-blue-800">
                    💡 <strong>{{ __('Подсказка:') }}</strong> {{ __('Пакеты выгоднее при регулярном объёме. Разовая оплата дороже на') }} {{ number_format($perUnitOption['breakdown']['surcharge_percent'] ?? 10, 0) }}% {{ __('из-за отсутствия планирования объемов.') }}
                </p>
            </div>
        </details>
        
        {{-- Step 4: Detailed Breakdown (Collapsible) --}}
        <details class="card" id="package-details">
            <summary class="cursor-pointer font-heading text-h4 flex items-center gap-2 hover:text-brand transition">
                <span>📋</span>
                @if($isPackage)
                <span>{{ __('Подробный расчет пакета') }} {{ $recommended['plan']->getName() }}</span>
                @else
                <span>{{ __('Подробный расчет оплаты по факту') }}</span>
                @endif
                <svg class="w-5 h-5 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </summary>
            
            <div class="mt-6 bg-white rounded-btn p-6">
                @if($isPackage)
                <div class="space-y-4">
                    <div class="flex justify-between items-center pb-3 border-b border-brand-border">
                        <span class="font-semibold">{{ __('Абонплата пакета') }} {{ $recommended['plan']->getName() }}</span>
                        <span class="text-brand font-semibold">{{ number_format($recommended['breakdown']['monthly_fee'], 0, '', ' ') }} {{ __('сум') }}</span>
                    </div>
                    
                    <div>
                        <h5 class="font-semibold mb-2">{{ __('Что входит в пакет:') }}</h5>
                        <ul class="text-body-s text-text-muted space-y-1 ml-4">
                            <li>✅ {{ __('До') }} {{ $recommended['plan']->fbs_shipments_included }} {{ __('FBS отправлений') }}</li>
                            @if($recommended['plan']->storage_included_boxes)
                            <li>✅ {{ __('До') }} {{ $recommended['plan']->storage_included_boxes }} {{ __('коробов на хранении') }}</li>
                            @endif
                            @if($recommended['plan']->storage_included_bags)
                            <li>✅ {{ __('До') }} {{ $recommended['plan']->storage_included_bags }} {{ __('мешков на хранении') }}</li>
                            @endif
                            @if($recommended['plan']->inbound_included_boxes)
                            <li>✅ {{ __('До') }} {{ $recommended['plan']->inbound_included_boxes }} {{ __('коробов приёмки') }}</li>
                            @endif
                        </ul>
                    </div>
                    
                    @if($recommended['breakdown']['overage']['total'] > 0)
                    <div class="bg-yellow-50 p-4 rounded-btn">
                        <h5 class="font-semibold mb-2 text-warning">{{ __('Доплата за превышение:') }}</h5>
                        <div class="space-y-2 text-body-s">
                            @if(isset($recommended['breakdown']['overage']['shipments']) && $recommended['breakdown']['overage']['shipments']['total'] > 0)
                            <div class="flex justify-between">
                                <span>{{ __('Отправления сверх лимита') }}</span>
                                <span class="font-semibold">{{ number_format($recommended['breakdown']['overage']['shipments']['total'], 0, '', ' ') }} {{ __('сум') }}</span>
                            </div>
                            @endif
                            @if(isset($recommended['breakdown']['overage']['storage']) && $recommended['breakdown']['overage']['storage'] > 0)
                            <div class="flex justify-between">
                                <span>{{ __('Хранение сверх лимита') }}</span>
                                <span class="font-semibold">{{ number_format($recommended['breakdown']['overage']['storage'], 0, '', ' ') }} {{ __('сум') }}</span>
                            </div>
                            @endif
                            @if(isset($recommended['breakdown']['overage']['inbound']) && $recommended['breakdown']['overage']['inbound'] > 0)
                            <div class="flex justify-between">
                                <span>{{ __('Приёмка сверх лимита') }}</span>
                                <span class="font-semibold">{{ number_format($recommended['breakdown']['overage']['inbound'], 0, '', ' ') }} {{ __('сум') }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                    
                    <div class="pt-4 border-t-2 border-brand-border flex justify-between items-center">
                        <span class="text-h4 font-heading">{{ __('Итого к оплате:') }}</span>
                        <span class="text-h3 text-brand font-bold">{{ number_format($recommended['total'], 0, '', ' ') }} {{ __('сум/мес') }}</span>
                    </div>
                </div>
                @else
                {{-- Per-unit detailed breakdown --}}
                <div class="space-y-3">
                    @if($perUnitOption['breakdown']['mgt']['count'] > 0)
                    <div class="flex justify-between items-center">
                        <span>MGT: {{ $perUnitOption['breakdown']['mgt']['count'] }} × {{ number_format($perUnitOption['breakdown']['mgt']['rate'], 0, '', ' ') }} {{ __('сум') }}</span>
                        <span class="font-semibold">{{ number_format($perUnitOption['breakdown']['mgt']['cost'], 0, '', ' ') }} {{ __('сум') }}</span>
                    </div>
                    @endif
                    
                    @if($perUnitOption['breakdown']['sgt']['count'] > 0)
                    <div class="flex justify-between items-center">
                        <span>SGT: {{ $perUnitOption['breakdown']['sgt']['count'] }} × {{ number_format($perUnitOption['breakdown']['sgt']['rate'], 0, '', ' ') }} {{ __('сум') }}</span>
                        <span class="font-semibold">{{ number_format($perUnitOption['breakdown']['sgt']['cost'], 0, '', ' ') }} {{ __('сум') }}</span>
                    </div>
                    @endif
                    
                    @if($perUnitOption['breakdown']['kgt']['count'] > 0)
                    <div class="flex justify-between items-center">
                        <span>KGT: {{ $perUnitOption['breakdown']['kgt']['count'] }} × {{ number_format($perUnitOption['breakdown']['kgt']['rate'], 0, '', ' ') }} {{ __('сум') }}</span>
                        <span class="font-semibold">{{ number_format($perUnitOption['breakdown']['kgt']['cost'], 0, '', ' ') }} {{ __('сум') }}</span>
                    </div>
                    @endif
                    
                    @if($result['usage']['storage_boxes'] > 0 || $result['usage']['storage_bags'] > 0)
                    <div class="flex justify-between items-center">
                        <span>{{ __('Хранение') }}: {{ $result['usage']['storage_boxes'] }} {{ __('коробов') }} + {{ $result['usage']['storage_bags'] }} {{ __('мешков') }}</span>
                        <span class="font-semibold">{{ number_format($perUnitOption['breakdown']['storage'], 0, '', ' ') }} {{ __('сум') }}</span>
                    </div>
                    @endif
                    
                    @if($result['usage']['inbound_boxes'] > 0)
                    <div class="flex justify-between items-center">
                        <span>{{ __('Приёмка') }}: {{ $result['usage']['inbound_boxes'] }} {{ __('коробов') }}</span>
                        <span class="font-semibold">{{ number_format($perUnitOption['breakdown']['inbound'], 0, '', ' ') }} {{ __('сум') }}</span>
                    </div>
                    @endif
                    
                    @if($perUnitOption['breakdown']['surcharge_percent'] > 0)
                    <div class="bg-yellow-50 p-3 rounded-btn text-body-s text-warning">
                        ⚠️ {{ __('В цены включена надбавка') }} +{{ number_format($perUnitOption['breakdown']['surcharge_percent'], 0) }}% {{ __('за отсутствие подписки') }}
                    </div>
                    @endif
                    
                    <div class="pt-4 border-t-2 border-brand-border flex justify-between items-center">
                        <span class="text-h4 font-heading">{{ __('Итого:') }}</span>
                        <span class="text-h3 text-brand font-bold">{{ number_format($perUnitOption['total'], 0, '', ' ') }} {{ __('сум/мес') }}</span>
                    </div>
                </div>
                @endif
                
                <div class="mt-6 space-y-2 text-body-s text-text-muted">
                    <p>* {{ __('Цены указаны за базовые услуги FBS, хранение и приёмку') }}</p>
                    <p>* {{ __('DBS и FBO услуги рассчитываются отдельно') }}</p>
                    <p>* {{ __('Окончательная стоимость зависит от фактического использования') }}</p>
                </div>
            </div>
        </details>
        
        @endif
    </div>
</section>
@endsection
