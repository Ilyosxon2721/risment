@extends('layouts.app')

@section('title', __('Calculator') . ' - RISMENT')

@section('content')
@php
    use App\Models\ContentBlock;
    use App\Services\PricingService;

    $explanation = ContentBlock::getBlock('calculator', 'explanation');

    // Load rates from database
    $pricingService = app(PricingService::class);
    $overageRates = $pricingService->getOverageRates();
    $dimensionCategories = config('pricing.dimension_categories');
@endphp

<section class="py-16">
    <div class="container-risment max-w-5xl">
        <h1 class="text-h1 font-heading text-center mb-4">{{ __('Calculator') }}</h1>

        @if($explanation)
        <p class="text-body-l text-text-muted text-center mb-12">{{ $explanation->getBody() }}</p>
        @endif

        {{-- Input Form --}}
        <form method="POST" action="{{ route('calculator.calculate', ['locale' => app()->getLocale()]) }}" class="card mb-8">
            @csrf

            {{-- FBS Delivery Size Breakdown --}}
            <div class="mb-6">
                <h3 class="text-h3 font-heading mb-2">{{ __('Логистика FBS (сборка + доставка)') }}</h3>
                <p class="text-body-s text-text-muted mb-4">{{ __('Укажите количество отправок по каждому размеру. Цена включает сборку заказа и довоз до ПВЗ маркетплейса.') }}</p>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-body-m font-semibold mb-2">
                            MICRO (≤{{ $dimensionCategories['micro']['max'] ?? 30 }} см)
                            <span class="text-brand ml-2">{{ number_format($overageRates['shipments']['micro_fee'] ?? 4000, 0, '', ' ') }} {{ __('сум/шт') }}</span>
                        </label>
                        <input type="number" name="micro_count" class="input" value="{{ old('micro_count', $result['usage']['micro_count'] ?? 0) }}" min="0" required>
                        <p class="text-body-s text-text-muted mt-1">{{ __('Мини посылки') }}</p>
                    </div>

                    <div>
                        <label class="block text-body-m font-semibold mb-2">
                            {{ __('МГТ') }} ({{ $dimensionCategories['mgt']['min'] ?? 31 }}-{{ $dimensionCategories['mgt']['max'] ?? 60 }} см)
                            <span class="text-brand ml-2">{{ number_format($overageRates['shipments']['mgt_fee'] ?? 8000, 0, '', ' ') }} {{ __('сум/шт') }}</span>
                        </label>
                        <input type="number" name="mgt_count" class="input" value="{{ old('mgt_count', $result['usage']['mgt_count'] ?? 0) }}" min="0" required>
                        <p class="text-body-s text-text-muted mt-1">{{ __('Малые посылки') }}</p>
                    </div>

                    <div>
                        <label class="block text-body-m font-semibold mb-2">
                            {{ __('СГТ') }} ({{ $dimensionCategories['sgt']['min'] ?? 61 }}-{{ $dimensionCategories['sgt']['max'] ?? 120 }} см)
                            <span class="text-brand ml-2">{{ number_format($overageRates['shipments']['sgt_fee'] ?? 15000, 0, '', ' ') }} {{ __('сум/шт') }}</span>
                        </label>
                        <input type="number" name="sgt_count" class="input" value="{{ old('sgt_count', $result['usage']['sgt_count'] ?? 0) }}" min="0" required>
                        <p class="text-body-s text-text-muted mt-1">{{ __('Средние посылки') }}</p>
                    </div>

                    <div>
                        <label class="block text-body-m font-semibold mb-2">
                            {{ __('КГТ') }} (>{{ $dimensionCategories['sgt']['max'] ?? 120 }} см)
                            <span class="text-brand ml-2">{{ number_format($overageRates['shipments']['kgt_fee'] ?? 35000, 0, '', ' ') }} {{ __('сум/шт') }}</span>
                        </label>
                        <input type="number" name="kgt_count" class="input" value="{{ old('kgt_count', $result['usage']['kgt_count'] ?? 0) }}" min="0" required>
                        <p class="text-body-s text-text-muted mt-1">{{ __('Крупные посылки') }}</p>
                    </div>
                </div>

                <p class="text-body-s text-text-muted mt-3">
                    💡 {{ __('Д+Ш+В = длина + ширина + высота в см. Цена уже включает сборку заказа и доставку до склада маркетплейса.') }}
                </p>
            </div>
            
            <hr class="my-8 border-brand-border">
            
            {{-- Expected Usage --}}
            <div class="mb-6">
                <h3 class="text-h3 font-heading mb-4">{{ __('Ожидаемые объёмы в месяц') }}</h3>
                <p class="text-body-s text-text-muted mb-4">{{ __('Укажите планируемое использование хранения и приёмки товара') }}</p>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-body-m font-semibold mb-2">{{ __('Короб-дни хранения') }}</label>
                        <input type="number" name="storage_box_days" class="input" value="{{ old('storage_box_days', $result['usage']['storage_box_days'] ?? 0) }}" min="0" required>
                        <p class="text-body-s text-text-muted mt-1">{{ __('Коробов 60×40×40 см × дни') }}</p>
                        <p class="text-body-xs text-text-muted mt-1">{{ __('Например: 10 коробов × 30 дней = 300') }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-body-m font-semibold mb-2">{{ __('Мешок-дни хранения') }}</label>
                        <input type="number" name="storage_bag_days" class="input" value="{{ old('storage_bag_days', $result['usage']['storage_bag_days'] ?? 0) }}" min="0" required>
                        <p class="text-body-s text-text-muted mt-1">{{ __('Мешков одежды × дни') }}</p>
                        <p class="text-body-xs text-text-muted mt-1">{{ __('Например: 5 мешков × 30 дней = 150') }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-body-m font-semibold mb-2">{{ __('Коробов приёмки') }}</label>
                        <input type="number" name="inbound_boxes" class="input" value="{{ old('inbound_boxes', $result['usage']['inbound_boxes'] ?? 0) }}" min="0" required>
                        <p class="text-body-s text-text-muted mt-1">{{ __('Поставки в месяц') }}</p>
                    </div>
                </div>
                
                {{-- Advanced option --}}
                <details class="mt-6">
                    <summary class="cursor-pointer text-body-m text-brand font-semibold">{{ __('⚙️ Дополнительные параметры') }}</summary>
                    <div class="mt-4 p-4 bg-bg-soft rounded-btn">
                        <label class="block text-body-m font-semibold mb-2">{{ __('Среднее количество позиций в заказе') }}</label>
                        <input type="number" step="0.1" name="avg_items_per_order" class="input max-w-xs" value="{{ old('avg_items_per_order', $result['usage']['avg_items_per_order'] ?? 1.0) }}" min="1.0" max="10.0">
                        <p class="text-body-s text-text-muted mt-1">{{ __('Если в одном заказе несколько товаров, укажите среднее значение (1.0 - 10.0)') }}</p>
                    </div>
                </details>
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
                            @php
                                $parts = [];
                                if (($result['usage']['micro_count'] ?? 0) > 0) $parts[] = $result['usage']['micro_count'] . ' MICRO';
                                if ($result['usage']['mgt_count'] > 0) $parts[] = $result['usage']['mgt_count'] . ' МГТ';
                                if ($result['usage']['sgt_count'] > 0) $parts[] = $result['usage']['sgt_count'] . ' СГТ';
                                if ($result['usage']['kgt_count'] > 0) $parts[] = $result['usage']['kgt_count'] . ' КГТ';
                            @endphp
                            @if(count($parts) > 0)
                                ({{ implode(', ', $parts) }})
                            @endif
                        </p>
                        @if($result['usage']['storage_box_days'] > 0 || $result['usage']['storage_bag_days'] > 0)
                        <p><strong>{{ __('Хранение:') }}</strong> {{ $result['usage']['storage_box_days'] }} {{ __('короб-дней') }} + {{ $result['usage']['storage_bag_days'] }} {{ __('мешок-дней') }}</p>
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
                <h2 class="text-xl sm:text-h1 font-heading text-brand mb-2">{{ __('Пакет') }} {{ $recommended['plan']->getName() }}</h2>
                <div class="text-3xl sm:text-5xl font-bold text-green-600 mb-2">
                    {{ number_format($recommended['total'], 0, '', ' ') }} <span class="text-lg sm:text-2xl">{{ __('сум/мес') }}</span>
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
                <h2 class="text-xl sm:text-h1 font-heading text-brand mb-2">{{ __('Оплата по факту') }}</h2>
                <div class="text-3xl sm:text-5xl font-bold text-green-600 mb-2">
                    {{ number_format($recommended['total'], 0, '', ' ') }} <span class="text-lg sm:text-2xl">{{ __('сум/мес') }}</span>
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
            
            <div class="mt-6 table-responsive relative">
                <table class="w-full responsive-table">
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
                            <td class="px-4 py-4" data-label="{{ __('Вариант') }}">
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
                            <td class="px-4 py-4 text-right font-semibold text-brand" data-label="{{ __('Стоимость') }}">
                                {{ number_format($option['total'], 0, '', ' ') }} <span class="text-body-s">{{ __('сум') }}</span>
                            </td>
                            <td class="px-4 py-4" data-label="{{ __('Особенности') }}">
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
                            <td class="px-4 py-4 text-right" data-label="">
                                @if($option['type'] === 'plan')
                                    <a href="{{ route('cabinet.subscription.choose', ['locale' => app()->getLocale()]) }}" class="btn btn-secondary btn-sm min-h-[44px]">
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
        
        {{-- Step 3.5: Side-by-Side Plan Comparison --}}
        <div class="card mb-6" id="compare-plans">
            <h3 class="text-h3 font-heading mb-2 flex items-center gap-2">
                <span>📊</span>
                <span>{{ __('Compare Plans') }}</span>
            </h3>
            <p class="text-body-s text-text-muted mb-6">{{ __('Side-by-side comparison of all available plans based on your volumes') }}</p>

            @php
                $planOptions = collect($result['comparison']['all_options'])->where('type', 'plan')->values();
                $perUnitOpt = collect($result['comparison']['all_options'])->where('type', 'per_unit')->first();
                $recommendedType = $result['comparison']['recommended']['type'];
                $recommendedCode = $recommendedType === 'plan' ? $result['comparison']['recommended']['plan']->code : null;
                $overageRatesCompare = $pricingService->getOverageRates();
            @endphp

            {{-- Desktop: horizontal scroll table --}}
            <div class="hidden md:block overflow-x-auto -mx-2 px-2">
                <table class="w-full text-body-s">
                    <thead>
                        <tr class="border-b-2 border-brand-border">
                            <th class="px-3 py-3 text-left text-body-m font-semibold min-w-[160px]">{{ __('Feature') }}</th>
                            @foreach($planOptions as $opt)
                            <th class="px-3 py-3 text-center min-w-[140px] {{ $recommendedCode === $opt['plan']->code ? 'bg-brand/5' : '' }}">
                                <div class="flex flex-col items-center gap-1">
                                    @if($recommendedCode === $opt['plan']->code)
                                    <span class="inline-block px-2 py-0.5 bg-brand text-white text-body-xs rounded-full">{{ __('Best value') }}</span>
                                    @endif
                                    <span class="text-body-m font-heading">{{ $opt['plan']->getName() }}</span>
                                </div>
                            </th>
                            @endforeach
                            <th class="px-3 py-3 text-center min-w-[140px] {{ $recommendedType === 'per_unit' ? 'bg-brand/5' : '' }}">
                                <div class="flex flex-col items-center gap-1">
                                    @if($recommendedType === 'per_unit')
                                    <span class="inline-block px-2 py-0.5 bg-brand text-white text-body-xs rounded-full">{{ __('Best value') }}</span>
                                    @endif
                                    <span class="text-body-m font-heading">{{ __('Per-unit rate') }}</span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-brand-border">
                        {{-- Monthly fee --}}
                        <tr>
                            <td class="px-3 py-3 font-semibold">{{ __('Monthly fee') }}</td>
                            @foreach($planOptions as $opt)
                            <td class="px-3 py-3 text-center {{ $recommendedCode === $opt['plan']->code ? 'bg-brand/5' : '' }}">
                                {{ number_format($opt['plan']->price_month, 0, '', ' ') }} {{ __('сум') }}
                            </td>
                            @endforeach
                            <td class="px-3 py-3 text-center {{ $recommendedType === 'per_unit' ? 'bg-brand/5' : '' }}">
                                0 {{ __('сум') }}
                            </td>
                        </tr>
                        {{-- FBS included --}}
                        <tr>
                            <td class="px-3 py-3 font-semibold">{{ __('FBS shipments included') }}</td>
                            @foreach($planOptions as $opt)
                            <td class="px-3 py-3 text-center {{ $recommendedCode === $opt['plan']->code ? 'bg-brand/5' : '' }}">
                                {{ $opt['plan']->fbs_shipments_included ?? '—' }} {{ __('pcs') }}
                            </td>
                            @endforeach
                            <td class="px-3 py-3 text-center {{ $recommendedType === 'per_unit' ? 'bg-brand/5' : '' }}">
                                —
                            </td>
                        </tr>
                        {{-- Price per MICRO --}}
                        <tr>
                            <td class="px-3 py-3 font-semibold">{{ __('MICRO shipment price') }}</td>
                            @foreach($planOptions as $opt)
                            <td class="px-3 py-3 text-center {{ $recommendedCode === $opt['plan']->code ? 'bg-brand/5' : '' }}">
                                {{ number_format($overageRatesCompare['shipments']['micro_fee'], 0, '', ' ') }} {{ __('сум') }}
                            </td>
                            @endforeach
                            <td class="px-3 py-3 text-center {{ $recommendedType === 'per_unit' ? 'bg-brand/5' : '' }}">
                                {{ number_format($perUnitOpt['breakdown']['micro']['rate_per_shipment'] ?? $overageRatesCompare['shipments']['micro_fee'], 0, '', ' ') }} {{ __('сум') }}
                            </td>
                        </tr>
                        {{-- Price per MGT --}}
                        <tr>
                            <td class="px-3 py-3 font-semibold">{{ __('MGT shipment price') }}</td>
                            @foreach($planOptions as $opt)
                            <td class="px-3 py-3 text-center {{ $recommendedCode === $opt['plan']->code ? 'bg-brand/5' : '' }}">
                                {{ number_format($overageRatesCompare['shipments']['mgt_fee'], 0, '', ' ') }} {{ __('сум') }}
                            </td>
                            @endforeach
                            <td class="px-3 py-3 text-center {{ $recommendedType === 'per_unit' ? 'bg-brand/5' : '' }}">
                                {{ number_format($perUnitOpt['breakdown']['mgt']['rate_per_shipment'] ?? $overageRatesCompare['shipments']['mgt_fee'], 0, '', ' ') }} {{ __('сум') }}
                            </td>
                        </tr>
                        {{-- Price per SGT --}}
                        <tr>
                            <td class="px-3 py-3 font-semibold">{{ __('SGT shipment price') }}</td>
                            @foreach($planOptions as $opt)
                            <td class="px-3 py-3 text-center {{ $recommendedCode === $opt['plan']->code ? 'bg-brand/5' : '' }}">
                                {{ number_format($overageRatesCompare['shipments']['sgt_fee'], 0, '', ' ') }} {{ __('сум') }}
                            </td>
                            @endforeach
                            <td class="px-3 py-3 text-center {{ $recommendedType === 'per_unit' ? 'bg-brand/5' : '' }}">
                                {{ number_format($perUnitOpt['breakdown']['sgt']['rate_per_shipment'] ?? $overageRatesCompare['shipments']['sgt_fee'], 0, '', ' ') }} {{ __('сум') }}
                            </td>
                        </tr>
                        {{-- Price per KGT --}}
                        <tr>
                            <td class="px-3 py-3 font-semibold">{{ __('KGT shipment price') }}</td>
                            @foreach($planOptions as $opt)
                            <td class="px-3 py-3 text-center {{ $recommendedCode === $opt['plan']->code ? 'bg-brand/5' : '' }}">
                                {{ number_format($overageRatesCompare['shipments']['kgt_fee'], 0, '', ' ') }} {{ __('сум') }}
                            </td>
                            @endforeach
                            <td class="px-3 py-3 text-center {{ $recommendedType === 'per_unit' ? 'bg-brand/5' : '' }}">
                                {{ number_format($perUnitOpt['breakdown']['kgt']['rate_per_shipment'] ?? $overageRatesCompare['shipments']['kgt_fee'], 0, '', ' ') }} {{ __('сум') }}
                            </td>
                        </tr>
                        {{-- Total cost --}}
                        <tr class="bg-bg-soft font-semibold">
                            <td class="px-3 py-3 text-body-m">{{ __('Total cost for your volumes') }}</td>
                            @foreach($planOptions as $opt)
                            <td class="px-3 py-3 text-center text-brand text-body-m {{ $recommendedCode === $opt['plan']->code ? 'bg-brand/10' : '' }}">
                                {{ number_format($opt['total'], 0, '', ' ') }} {{ __('сум') }}
                            </td>
                            @endforeach
                            <td class="px-3 py-3 text-center text-brand text-body-m {{ $recommendedType === 'per_unit' ? 'bg-brand/10' : '' }}">
                                {{ number_format($perUnitOpt['total'], 0, '', ' ') }} {{ __('сум') }}
                            </td>
                        </tr>
                        {{-- Savings vs per-unit --}}
                        <tr>
                            <td class="px-3 py-3 font-semibold">{{ __('Savings vs per-unit') }}</td>
                            @foreach($planOptions as $opt)
                            <td class="px-3 py-3 text-center {{ $recommendedCode === $opt['plan']->code ? 'bg-brand/5' : '' }}">
                                @if($opt['savings_vs_per_unit'] > 0)
                                    <span class="text-green-600 font-semibold">-{{ number_format($opt['savings_vs_per_unit'], 0, '', ' ') }} {{ __('сум') }}</span>
                                    <div class="text-body-xs text-green-600">({{ number_format($opt['savings_percent'], 1) }}%)</div>
                                @elseif($opt['savings_vs_per_unit'] < 0)
                                    <span class="text-red-500">+{{ number_format(abs($opt['savings_vs_per_unit']), 0, '', ' ') }} {{ __('сум') }}</span>
                                @else
                                    <span class="text-text-muted">—</span>
                                @endif
                            </td>
                            @endforeach
                            <td class="px-3 py-3 text-center {{ $recommendedType === 'per_unit' ? 'bg-brand/5' : '' }}">
                                <span class="text-text-muted">—</span>
                            </td>
                        </tr>
                        {{-- Features: Priority processing --}}
                        <tr>
                            <td class="px-3 py-3 font-semibold">{{ __('Priority processing') }}</td>
                            @foreach($planOptions as $opt)
                            <td class="px-3 py-3 text-center {{ $recommendedCode === $opt['plan']->code ? 'bg-brand/5' : '' }}">
                                @if($opt['plan']->priority_processing)
                                    <span class="text-green-500 text-lg">✅</span>
                                @else
                                    <span class="text-text-muted">—</span>
                                @endif
                            </td>
                            @endforeach
                            <td class="px-3 py-3 text-center {{ $recommendedType === 'per_unit' ? 'bg-brand/5' : '' }}">
                                <span class="text-text-muted">—</span>
                            </td>
                        </tr>
                        {{-- Features: Personal manager --}}
                        <tr>
                            <td class="px-3 py-3 font-semibold">{{ __('Personal manager') }}</td>
                            @foreach($planOptions as $opt)
                            <td class="px-3 py-3 text-center {{ $recommendedCode === $opt['plan']->code ? 'bg-brand/5' : '' }}">
                                @if($opt['plan']->personal_manager)
                                    <span class="text-green-500 text-lg">✅</span>
                                @else
                                    <span class="text-text-muted">—</span>
                                @endif
                            </td>
                            @endforeach
                            <td class="px-3 py-3 text-center {{ $recommendedType === 'per_unit' ? 'bg-brand/5' : '' }}">
                                <span class="text-text-muted">—</span>
                            </td>
                        </tr>
                        {{-- Storage included --}}
                        <tr>
                            <td class="px-3 py-3 font-semibold">{{ __('Storage included') }}</td>
                            @foreach($planOptions as $opt)
                            <td class="px-3 py-3 text-center {{ $recommendedCode === $opt['plan']->code ? 'bg-brand/5' : '' }}">
                                @if($opt['plan']->storage_included_boxes || $opt['plan']->storage_included_bags)
                                    @if($opt['plan']->storage_included_boxes)
                                        {{ $opt['plan']->storage_included_boxes }} {{ __('Boxes') }}
                                    @endif
                                    @if($opt['plan']->storage_included_bags)
                                        {{ $opt['plan']->storage_included_bags }} {{ __('Bags') }}
                                    @endif
                                @else
                                    <span class="text-text-muted">—</span>
                                @endif
                            </td>
                            @endforeach
                            <td class="px-3 py-3 text-center {{ $recommendedType === 'per_unit' ? 'bg-brand/5' : '' }}">
                                <span class="text-text-muted">—</span>
                            </td>
                        </tr>
                        {{-- Inbound included --}}
                        <tr>
                            <td class="px-3 py-3 font-semibold">{{ __('Receiving included') }}</td>
                            @foreach($planOptions as $opt)
                            <td class="px-3 py-3 text-center {{ $recommendedCode === $opt['plan']->code ? 'bg-brand/5' : '' }}">
                                @if($opt['plan']->inbound_included_boxes)
                                    {{ $opt['plan']->inbound_included_boxes }} {{ __('Boxes') }}
                                @else
                                    <span class="text-text-muted">—</span>
                                @endif
                            </td>
                            @endforeach
                            <td class="px-3 py-3 text-center {{ $recommendedType === 'per_unit' ? 'bg-brand/5' : '' }}">
                                <span class="text-text-muted">—</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Mobile: card layout --}}
            <div class="md:hidden space-y-4">
                @foreach($planOptions as $opt)
                <div class="rounded-btn border {{ $recommendedCode === $opt['plan']->code ? 'border-2 border-brand shadow-lg' : 'border-brand-border' }} p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-body-l font-heading">{{ $opt['plan']->getName() }}</h4>
                        @if($recommendedCode === $opt['plan']->code)
                        <span class="px-2 py-0.5 bg-brand text-white text-body-xs rounded-full">{{ __('Best value') }}</span>
                        @endif
                    </div>
                    <div class="text-h4 text-brand font-bold mb-3">{{ number_format($opt['total'], 0, '', ' ') }} {{ __('сум/мес') }}</div>
                    <dl class="space-y-2 text-body-s">
                        <div class="flex justify-between">
                            <dt class="text-text-muted">{{ __('Monthly fee') }}</dt>
                            <dd class="font-semibold">{{ number_format($opt['plan']->price_month, 0, '', ' ') }} {{ __('сум') }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-text-muted">{{ __('FBS shipments included') }}</dt>
                            <dd class="font-semibold">{{ $opt['plan']->fbs_shipments_included ?? '—' }} {{ __('pcs') }}</dd>
                        </div>
                        @if($opt['savings_vs_per_unit'] > 0)
                        <div class="flex justify-between">
                            <dt class="text-text-muted">{{ __('Savings vs per-unit') }}</dt>
                            <dd class="text-green-600 font-semibold">-{{ number_format($opt['savings_vs_per_unit'], 0, '', ' ') }} ({{ number_format($opt['savings_percent'], 1) }}%)</dd>
                        </div>
                        @endif
                        <div class="flex justify-between items-center">
                            <dt class="text-text-muted">{{ __('Priority processing') }}</dt>
                            <dd>{{ $opt['plan']->priority_processing ? '✅' : '—' }}</dd>
                        </div>
                        <div class="flex justify-between items-center">
                            <dt class="text-text-muted">{{ __('Personal manager') }}</dt>
                            <dd>{{ $opt['plan']->personal_manager ? '✅' : '—' }}</dd>
                        </div>
                    </dl>
                </div>
                @endforeach

                {{-- Per-unit card --}}
                <div class="rounded-btn border {{ $recommendedType === 'per_unit' ? 'border-2 border-brand shadow-lg' : 'border-brand-border' }} p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-body-l font-heading">{{ __('Per-unit rate') }}</h4>
                        @if($recommendedType === 'per_unit')
                        <span class="px-2 py-0.5 bg-brand text-white text-body-xs rounded-full">{{ __('Best value') }}</span>
                        @endif
                    </div>
                    <div class="text-h4 text-brand font-bold mb-3">{{ number_format($perUnitOpt['total'], 0, '', ' ') }} {{ __('сум/мес') }}</div>
                    <dl class="space-y-2 text-body-s">
                        <div class="flex justify-between">
                            <dt class="text-text-muted">{{ __('Monthly fee') }}</dt>
                            <dd class="font-semibold">0 {{ __('сум') }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-text-muted">{{ __('FBS shipments included') }}</dt>
                            <dd class="font-semibold">—</dd>
                        </div>
                        @if($perUnitOpt['breakdown']['surcharge_percent'] > 0)
                        <div class="flex justify-between">
                            <dt class="text-text-muted">{{ __('Surcharge') }}</dt>
                            <dd class="text-warning font-semibold">+{{ number_format($perUnitOpt['breakdown']['surcharge_percent'], 0) }}%</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>

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
                    @if(($perUnitOption['breakdown']['micro']['count'] ?? 0) > 0)
                    <div class="flex justify-between items-center">
                        <span>MICRO: {{ $perUnitOption['breakdown']['micro']['count'] }} × {{ number_format($perUnitOption['breakdown']['micro']['rate_per_shipment'], 0, '', ' ') }} {{ __('сум') }}</span>
                        <span class="font-semibold">{{ number_format($perUnitOption['breakdown']['micro']['total'], 0, '', ' ') }} {{ __('сум') }}</span>
                    </div>
                    @endif

                    @if($perUnitOption['breakdown']['mgt']['count'] > 0)
                    <div class="flex justify-between items-center">
                        <span>МГТ: {{ $perUnitOption['breakdown']['mgt']['count'] }} × {{ number_format($perUnitOption['breakdown']['mgt']['rate_per_shipment'], 0, '', ' ') }} {{ __('сум') }}</span>
                        <span class="font-semibold">{{ number_format($perUnitOption['breakdown']['mgt']['total'], 0, '', ' ') }} {{ __('сум') }}</span>
                    </div>
                    @endif

                    @if($perUnitOption['breakdown']['sgt']['count'] > 0)
                    <div class="flex justify-between items-center">
                        <span>СГТ: {{ $perUnitOption['breakdown']['sgt']['count'] }} × {{ number_format($perUnitOption['breakdown']['sgt']['rate_per_shipment'], 0, '', ' ') }} {{ __('сум') }}</span>
                        <span class="font-semibold">{{ number_format($perUnitOption['breakdown']['sgt']['total'], 0, '', ' ') }} {{ __('сум') }}</span>
                    </div>
                    @endif

                    @if($perUnitOption['breakdown']['kgt']['count'] > 0)
                    <div class="flex justify-between items-center">
                        <span>КГТ: {{ $perUnitOption['breakdown']['kgt']['count'] }} × {{ number_format($perUnitOption['breakdown']['kgt']['rate_per_shipment'], 0, '', ' ') }} {{ __('сум') }}</span>
                        <span class="font-semibold">{{ number_format($perUnitOption['breakdown']['kgt']['total'], 0, '', ' ') }} {{ __('сум') }}</span>
                    </div>
                    @endif
                    
                    @if($result['usage']['storage_box_days'] > 0 || $result['usage']['storage_bag_days'] > 0)
                    <div class="flex justify-between items-center">
                        <span>{{ __('Хранение') }}: {{ $result['usage']['storage_box_days'] }} {{ __('короб-дней') }} + {{ $result['usage']['storage_bag_days'] }} {{ __('мешок-дней') }}</span>
                        <span class="font-semibold">{{ number_format($perUnitOption['breakdown']['storage']['cost'], 0, '', ' ') }} {{ __('сум') }}</span>
                    </div>
                    @endif
                    
                    @if($result['usage']['inbound_boxes'] > 0)
                    <div class="flex justify-between items-center">
                        <span>{{ __('Приёмка') }}: {{ $result['usage']['inbound_boxes'] }} {{ __('коробов') }}</span>
                        <span class="font-semibold">{{ number_format($perUnitOption['breakdown']['inbound']['cost'], 0, '', ' ') }} {{ __('сум') }}</span>
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

        {{-- Email Results Form --}}
        <div class="card mt-6">
            <div class="flex items-start gap-3">
                <div class="text-2xl">📧</div>
                <div class="flex-1">
                    <h3 class="text-h4 font-heading mb-2">{{ __('Send results to email') }}</h3>
                    <p class="text-body-s text-text-muted mb-4">{{ __('Receive a detailed breakdown of the calculation to your email') }}</p>

                    @if(session('email_sent'))
                    <div class="p-3 bg-green-50 border border-green-300 rounded-btn text-green-700 text-body-m mb-4">
                        {{ session('email_sent') }}
                    </div>
                    @endif

                    @if(session('email_error'))
                    <div class="p-3 bg-red-50 border border-red-300 rounded-btn text-red-700 text-body-m mb-4">
                        {{ session('email_error') }}
                    </div>
                    @endif

                    @error('email')
                    <div class="p-3 bg-red-50 border border-red-300 rounded-btn text-red-700 text-body-m mb-4">
                        {{ $message }}
                    </div>
                    @enderror

                    <form method="POST" action="{{ route('calculator.send-email', ['locale' => app()->getLocale()]) }}" class="flex flex-col sm:flex-row gap-3">
                        @csrf
                        <input type="hidden" name="micro_count" value="{{ $result['usage']['micro_count'] ?? 0 }}">
                        <input type="hidden" name="mgt_count" value="{{ $result['usage']['mgt_count'] ?? 0 }}">
                        <input type="hidden" name="sgt_count" value="{{ $result['usage']['sgt_count'] ?? 0 }}">
                        <input type="hidden" name="kgt_count" value="{{ $result['usage']['kgt_count'] ?? 0 }}">
                        <input type="hidden" name="storage_box_days" value="{{ $result['usage']['storage_box_days'] ?? 0 }}">
                        <input type="hidden" name="storage_bag_days" value="{{ $result['usage']['storage_bag_days'] ?? 0 }}">
                        <input type="hidden" name="inbound_boxes" value="{{ $result['usage']['inbound_boxes'] ?? 0 }}">
                        <input type="hidden" name="avg_items_per_order" value="{{ $result['usage']['avg_items_per_order'] ?? 1.0 }}">

                        <input type="email" name="email" class="input flex-1" placeholder="{{ __('Enter your email') }}" value="{{ old('email') }}" required>
                        <button type="submit" class="btn btn-primary whitespace-nowrap">{{ __('Send to email') }}</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Save to Account (authenticated users only) --}}
        @auth
        <div class="card mt-6">
            <div class="flex items-start gap-3">
                <div class="text-2xl">💾</div>
                <div class="flex-1">
                    <h3 class="text-h4 font-heading mb-2">{{ __('Save to account') }}</h3>
                    <p class="text-body-s text-text-muted mb-4">{{ __('Save this calculation to your account for future reference') }}</p>

                    @if(session('success'))
                    <div class="p-3 bg-green-50 border border-green-300 rounded-btn text-green-700 text-body-m mb-4">
                        {{ session('success') }}
                    </div>
                    @endif

                    <form method="POST" action="{{ route('calculator.save', ['locale' => app()->getLocale()]) }}" class="flex flex-col sm:flex-row gap-3">
                        @csrf
                        <input type="hidden" name="micro_count" value="{{ $result['usage']['micro_count'] ?? 0 }}">
                        <input type="hidden" name="mgt_count" value="{{ $result['usage']['mgt_count'] ?? 0 }}">
                        <input type="hidden" name="sgt_count" value="{{ $result['usage']['sgt_count'] ?? 0 }}">
                        <input type="hidden" name="kgt_count" value="{{ $result['usage']['kgt_count'] ?? 0 }}">
                        <input type="hidden" name="storage_box_days" value="{{ $result['usage']['storage_box_days'] ?? 0 }}">
                        <input type="hidden" name="storage_bag_days" value="{{ $result['usage']['storage_bag_days'] ?? 0 }}">
                        <input type="hidden" name="inbound_boxes" value="{{ $result['usage']['inbound_boxes'] ?? 0 }}">
                        <input type="hidden" name="avg_items_per_order" value="{{ $result['usage']['avg_items_per_order'] ?? 1.0 }}">
                        <input type="hidden" name="recommended_plan" value="{{ $result['comparison']['recommended']['type'] === 'plan' ? $result['comparison']['recommended']['plan']->getName() : '' }}">
                        <input type="hidden" name="total_cost" value="{{ (int) ($result['comparison']['recommended']['total'] ?? 0) }}">
                        <input type="hidden" name="result_data" value="{{ json_encode($result['comparison']['recommended']) }}">

                        <input type="text" name="name" class="input flex-1" placeholder="{{ __('Calculation name (optional)') }}" maxlength="255">
                        <button type="submit" class="btn btn-primary whitespace-nowrap">{{ __('Save calculation') }}</button>
                    </form>
                </div>
            </div>
        </div>
        @endauth

        {{-- Step 5: Visual Charts --}}
        <div class="card mt-6" id="charts-section">
            <h3 class="text-h3 font-heading mb-6 text-center">{{ __('Visual Analysis') }}</h3>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                {{-- Pie Chart: Cost Distribution --}}
                <div>
                    <h4 class="text-h4 font-heading mb-4 text-center">{{ __('Cost Distribution') }}</h4>
                    <div class="relative" style="max-height: 350px;">
                        <canvas id="costPieChart"></canvas>
                    </div>
                </div>

                {{-- Bar Chart: Plan Comparison --}}
                <div>
                    <h4 class="text-h4 font-heading mb-4 text-center">{{ __('Plan Comparison') }}</h4>
                    <div class="relative" style="max-height: 350px;">
                        <canvas id="planBarChart"></canvas>
                    </div>
                </div>
            </div>

            <p class="text-body-s text-text-muted mt-6 text-center">
                {{ __('Charts show estimated monthly costs based on your input parameters') }}
            </p>
        </div>

        @endif
    </div>
</section>

@if(isset($result))
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const brandColor = '#CB4FE4';
    const brandDark = '#8E2BC6';
    const successColor = '#0ABB87';
    const warningColor = '#FFA800';
    const errorColor = '#F1416C';
    const infoColor = '#009EF7';
    const mutedText = '#5E6278';

    // --- Pie Chart: Cost Distribution ---
    @php
        $perUnitOption = collect($result['comparison']['all_options'])->where('type', 'per_unit')->first();
        $pieData = [];
        $pieLabels = [];
        $pieColors = [];

        // FBS categories
        if (($perUnitOption['breakdown']['micro']['total'] ?? 0) > 0) {
            $pieLabels[] = 'MICRO FBS';
            $pieData[] = $perUnitOption['breakdown']['micro']['total'];
            $pieColors[] = '#CB4FE4';
        }
        if (($perUnitOption['breakdown']['mgt']['total'] ?? 0) > 0) {
            $pieLabels[] = __('MGT FBS');
            $pieData[] = $perUnitOption['breakdown']['mgt']['total'];
            $pieColors[] = '#8E2BC6';
        }
        if (($perUnitOption['breakdown']['sgt']['total'] ?? 0) > 0) {
            $pieLabels[] = __('SGT FBS');
            $pieData[] = $perUnitOption['breakdown']['sgt']['total'];
            $pieColors[] = '#009EF7';
        }
        if (($perUnitOption['breakdown']['kgt']['total'] ?? 0) > 0) {
            $pieLabels[] = __('LGT FBS');
            $pieData[] = $perUnitOption['breakdown']['kgt']['total'];
            $pieColors[] = '#FFA800';
        }
        if (($perUnitOption['breakdown']['storage']['cost'] ?? 0) > 0) {
            $pieLabels[] = __('Storage');
            $pieData[] = $perUnitOption['breakdown']['storage']['cost'];
            $pieColors[] = '#0ABB87';
        }
        if (($perUnitOption['breakdown']['inbound']['cost'] ?? 0) > 0) {
            $pieLabels[] = __('Receiving');
            $pieData[] = $perUnitOption['breakdown']['inbound']['cost'];
            $pieColors[] = '#F1416C';
        }

        // Bar chart data
        $barLabels = [];
        $barTotals = [];
        $barBaseCosts = [];
        $barOverageCosts = [];
        $barBgColors = [];
        $recommendedType = $result['comparison']['recommended']['type'] ?? '';
        $recommendedCode = '';
        if ($recommendedType === 'plan' && isset($result['comparison']['recommended']['plan'])) {
            $recommendedCode = $result['comparison']['recommended']['plan']->code;
        }

        foreach ($result['comparison']['all_options'] as $option) {
            if ($option['type'] === 'plan') {
                $barLabels[] = $option['plan']->getName();
                $barTotals[] = $option['total'];
                $barBaseCosts[] = $option['breakdown']['monthly_fee'];
                $barOverageCosts[] = $option['breakdown']['overage']['total'] ?? 0;
                $isRecommended = $recommendedType === 'plan' && $option['plan']->code === $recommendedCode;
                $barBgColors[] = $isRecommended ? '#0ABB87' : '#CB4FE4';
            } else {
                $barLabels[] = __('Per-unit rate');
                $barTotals[] = $option['total'];
                $barBaseCosts[] = $option['total'];
                $barOverageCosts[] = 0;
                $isRecommended = $recommendedType === 'per_unit';
                $barBgColors[] = $isRecommended ? '#0ABB87' : '#5E6278';
            }
        }
    @endphp

    // Pie Chart
    const pieCtx = document.getElementById('costPieChart').getContext('2d');
    new Chart(pieCtx, {
        type: 'doughnut',
        data: {
            labels: @json($pieLabels),
            datasets: [{
                data: @json($pieData),
                backgroundColor: @json($pieColors),
                borderWidth: 2,
                borderColor: '#FFFFFF',
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 16,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        font: { family: 'Inter, sans-serif', size: 13 },
                        color: mutedText
                    }
                },
                tooltip: {
                    backgroundColor: '#0B0B10',
                    titleFont: { family: 'Manrope, sans-serif', weight: '600' },
                    bodyFont: { family: 'Inter, sans-serif' },
                    padding: 12,
                    cornerRadius: 12,
                    callbacks: {
                        label: function(context) {
                            const value = context.parsed;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const pct = ((value / total) * 100).toFixed(1);
                            return context.label + ': ' + new Intl.NumberFormat('ru-RU').format(value) + ' {{ __("сум") }} (' + pct + '%)';
                        }
                    }
                }
            }
        }
    });

    // Bar Chart
    const barCtx = document.getElementById('planBarChart').getContext('2d');
    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: @json($barLabels),
            datasets: [
                {
                    label: '{{ __("Base cost") }}',
                    data: @json($barBaseCosts),
                    backgroundColor: @json($barBgColors).map(c => c + 'CC'),
                    borderColor: @json($barBgColors),
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false
                },
                {
                    label: '{{ __("Overage charges") }}',
                    data: @json($barOverageCosts),
                    backgroundColor: warningColor + '99',
                    borderColor: warningColor,
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                x: {
                    stacked: true,
                    grid: { display: false },
                    ticks: {
                        font: { family: 'Inter, sans-serif', size: 11 },
                        color: mutedText,
                        maxRotation: 45
                    }
                },
                y: {
                    stacked: true,
                    beginAtZero: true,
                    grid: { color: '#E9ECF2' },
                    ticks: {
                        font: { family: 'Inter, sans-serif', size: 12 },
                        color: mutedText,
                        callback: function(value) {
                            return new Intl.NumberFormat('ru-RU', { notation: 'compact' }).format(value);
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 16,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        font: { family: 'Inter, sans-serif', size: 13 },
                        color: mutedText
                    }
                },
                tooltip: {
                    backgroundColor: '#0B0B10',
                    titleFont: { family: 'Manrope, sans-serif', weight: '600' },
                    bodyFont: { family: 'Inter, sans-serif' },
                    padding: 12,
                    cornerRadius: 12,
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + new Intl.NumberFormat('ru-RU').format(context.parsed.y) + ' {{ __("сум") }}';
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endif
@endsection
