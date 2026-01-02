@extends('layouts.app')

@section('title', __('Pricing') . ' - RISMENT')

@section('content')
@php
    use App\Models\SubscriptionPlan;
    use App\Models\ContentBlock;
    
    // Get subscription plans
    $plans = SubscriptionPlan::where('is_active', true)->orderBy('sort')->get();
    
    // Get CMS content
    $includedTitle = ContentBlock::getBlock('pricing', 'included_title');
    $includedBullets = ContentBlock::getBlock('pricing', 'included_bullets');
    $notIncludedTitle = ContentBlock::getBlock('pricing', 'not_included_title');
    $notIncludedBullets = ContentBlock::getBlock('pricing', 'not_included_bullets');
    $overagesTitle = ContentBlock::getBlock('pricing', 'overages_title');
    $overagesBullets = ContentBlock::getBlock('pricing', 'overages_bullets');
    $scheduleTitle = ContentBlock::getBlock('pricing', 'schedule_title');
    $scheduleBody = ContentBlock::getBlock('pricing', 'schedule_body');
    $policyUpgrade = ContentBlock::getBlock('pricing', 'policy_upgrade');
    
    // Get plan taglines
    $planTaglines = [];
    foreach($plans as $plan) {
        $tagline = ContentBlock::getBlock('pricing', 'plan_' . $plan->code . '_tagline');
        if ($tagline) {
            $planTaglines[$plan->code] = $tagline->getBody();
        }
    }
@endphp

<!-- Hero -->
<section class="gradient-brand text-white py-20">
    <div class="container-risment">
        <h1 class="text-h1 font-heading mb-4">{{ __('Pricing & Rates') }}</h1>
        <p class="text-body-l max-w-2xl">{{ __('Transparent pricing for all services. No hidden fees.') }}</p>
    </div>
</section>

<!-- Monthly Packages -->
<section class="py-16 bg-bg-soft">
    <div class="container-risment">
        <h2 class="text-h2 font-heading text-center mb-3">{{ __('Пакеты') }}</h2>
        <p class="text-body-l text-text-muted text-center mb-12 max-w-3xl mx-auto">
            {{ __('Выберите пакет с фиксированной абонплатой. Все включено: FBS обработка, хранение, доставка 3 раза в неделю.') }}
        </p>
        
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6 mb-12">
            @foreach($plans as $plan)
            <div class="card bg-white {{ $plan->code === 'pro' ? 'border-2 border-brand' : '' }} relative flex flex-col" style="min-height: 400px;">
                @if($plan->code === 'pro')
                <div class="absolute -top-3 -right-3 px-4 py-2 bg-white border-2 border-brand text-brand text-body-s font-semibold rounded-btn shadow-lg">
                    {{ __('Популярный') }}
                </div>
                @endif
                
                <div class="flex-grow">
                    <h3 class="text-h4 font-heading mb-2">{{ $plan->getName() }}</h3>
                    
                    @if(isset($planTaglines[$plan->code]))
                    <p class="text-body-s text-text-muted mb-4">{{ $planTaglines[$plan->code] }}</p>
                    @endif
                    
                    <div class="mb-4">
                        @if($plan->is_custom)
                            <div class="text-h3 font-heading text-brand">{{ __('от') }} {{ number_format($plan->min_price_month, 0, '', ' ') }}</div>
                            <div class="text-body-s text-text-muted">{{ __('сум/мес') }}</div>
                            <div class="text-body-s text-brand mt-1">{{ __('(индивидуально)') }}</div>
                        @else
                            <div class="text-h3 font-heading text-brand">{{ number_format($plan->price_month, 0, '', ' ') }}</div>
                            <div class="text-body-s text-text-muted">{{ __('сум/мес') }}</div>
                        @endif
                    </div>
                    
                    <div class="space-y-2 mb-4 text-body-s">
                        @if(!$plan->is_custom)
                            @if($plan->fbs_shipments_included)
                            <div class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-brand mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span>{{ __('FBS:') }} {{ $plan->fbs_shipments_included }} {{ __('шт') }}</span>
                            </div>
                            @endif
                            
                            @if($plan->storage_included_boxes)
                            <div class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-brand mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span>{{ __('Коробов:') }} {{ $plan->storage_included_boxes }}</span>
                            </div>
                            @endif
                            
                            @if($plan->storage_included_bags)
                            <div class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-brand mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span>{{ __('Мешков:') }} {{ $plan->storage_included_bags }}</span>
                            </div>
                            @endif
                        @endif
                        
                        @if($plan->shipping_included)
                        <div class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-brand mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span>{{ __('Доставка FBS') }}</span>
                        </div>
                        @endif
                        
                        @if($plan->priority_processing)
                        <div class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-brand mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span>{{ __('Приоритет') }}</span>
                        </div>
                        @endif
                        
                        @if($plan->personal_manager)
                        <div class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-brand mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span>{{ __('Менеджер') }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Buttons pinned at bottom -->
                <div class="mt-auto pt-4">
                    @if($plan->is_custom)
                    <a href="{{ route('contacts', ['locale' => app()->getLocale()]) }}" class="btn btn-secondary w-full text-body-s py-2">
                        {{ __('Связаться') }}
                    </a>
                    @else
                    <a href="{{ route('calculator', ['locale' => app()->getLocale()]) }}" class="btn {{ $plan->code === 'pro' ? 'btn-primary' : 'btn-secondary' }} w-full text-body-s py-2">
                        {{ __('Выбрать') }}
                    </a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Overages & Schedule in one row -->
        @if($overagesBullets || $scheduleBody)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @if(isset($overages))
            <div class="card bg-white">
                <h3 class="text-h4 font-heading mb-4">{{ __('Limitdan oshsa ortiqcha to\'lovlar') }}</h3>
                <div class="space-y-3 text-body-s">
                    {{-- FBS Shipments Overage by Category --}}
                    <div>
                        <div class="font-semibold mb-1">{{ __('FBS jo\'natmalar limiti oshsa') }}:</div>
                        <ul class="ml-4 space-y-1">
                            <li>MGT — {{ number_format($overages['shipments']['mgt_fee'], 0, '', ' ') }} {{ __('so\'m/jo\'natma') }}</li>
                            <li>SGT — {{ number_format($overages['shipments']['sgt_fee'], 0, '', ' ') }} {{ __('so\'m/jo\'natma') }}</li>
                            <li>KGT — {{ number_format($overages['shipments']['kgt_fee'], 0, '', ' ') }} {{ __('so\'m/jo\'natma') }}</li>
                        </ul>
                    </div>
                    
                    {{-- Storage Overage --}}
                    <div class="flex items-start gap-2">
                        <span class="text-brand">+</span>
                        <span>{{ __('Saqlash limiti oshsa') }}: {{ number_format($overages['storage']['box_rate'], 0, '', ' ') }} {{ __('so\'m/korob/oy') }}</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="text-brand">+</span>
                        <span>{{ __('Saqlash limiti oshsa') }}: {{ number_format($overages['storage']['bag_rate'], 0, '', ' ') }} {{ __('so\'m/qop/oy') }}</span>
                    </div>
                    
                    {{-- Inbound Overage --}}
                    <div class="flex items-start gap-2">
                        <span class="text-brand">+</span>
                        <span>{{ __('Qabul (inbound) limiti oshsa') }}: {{ number_format($overages['inbound']['box_rate'], 0, '', ' ') }} {{ __('so\'m/korob') }}</span>
                    </div>
                </div>
            </div>
            @endif
            
            @if($scheduleBody)
            <div class="card bg-white">
                <h3 class="text-h4 font-heading mb-4">{{ $scheduleTitle?->getTitle() ?? 'График' }}</h3>
                <p class="text-body-s">{{ $scheduleBody->getBody() }}</p>
                @if($policyUpgrade)
                <div class="mt-4 pt-4 border-t border-brand-border">
                    <p class="text-body-s text-text-muted">{{ $policyUpgrade->getBody() }}</p>
                </div>
                @endif
            </div>
            @endif
        </div>
        @endif
    </div>
</section>

<!-- Service Pricing Tables -->
<section class="py-16">
    <div class="container-risment">
        <!-- Logistics Pricing -->
        <div class="mb-16">
            <h2 class="text-h2 font-heading mb-2">{{ __('Logistics (Inbound + Storage)') }}</h2>
            <p class="text-body-m text-text-muted mb-8">{{ __('Cost depends on product size category') }}</p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @php
                    $logistics = [
                        ['category' => 'MGT', 'size' => '≤60 см', 'price' => '5,000'],
                        ['category' => 'SGT', 'size' => '61-120 см', 'price' => '7,500'],
                        ['category' => 'KGT', 'size' => '>120 см', 'price' => '12,000'],
                    ];
                @endphp
                
                @foreach($logistics as $item)
                <div class="card">
                    <div class="inline-block px-4 py-2 bg-brand/10 text-brand rounded-btn font-semibold mb-4">
                        {{ $item['category'] }}
                    </div>
                    <div class="text-body-s text-text-muted mb-2">{{ __('Sum of L+W+H') }}</div>
                    <div class="text-h3 font-heading mb-4">{{ $item['size'] }}</div>
                    <div class="text-price text-brand">{{ $item['price'] }} UZS</div>
                    <div class="text-body-s text-text-muted mt-1">{{ __('per unit') }}</div>
                </div>
                @endforeach
            </div>
        </div>
        
        <!-- Pick & Pack Pricing -->
        <div class="mb-16">
            <h2 class="text-h2 font-heading mb-2">{{ __('Pick & Pack (Order Assembly)') }}</h2>
            <p class="text-body-m text-text-muted mb-8">{{ __('Cost per order, regardless of size') }}</p>
            
            <div class="card max-w-2xl">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <div class="font-semibold text-body-l">{{ __('First item in order') }}</div>
                        <div class="text-body-s text-text-muted">{{ __('Picking, packing, labeling') }}</div>
                    </div>
                    <div class="text-price text-brand">7,000 UZS</div>
                </div>
                <div class="border-t border-brand-border pt-4 flex justify-between items-center">
                    <div>
                        <div class="font-semibold text-body-l">{{ __('Each additional item') }}</div>
                        <div class="text-body-s text-text-muted">{{ __('Additional SKU in same order') }}</div>
                    </div>
                    <div class="text-price text-brand">3,000 UZS</div>
                </div>
            </div>
        </div>
        
        <!-- FBO Shipping -->
        <div class="mb-16">
            <h2 class="text-h2 font-heading mb-2">{{ __('FBO Shipping to Marketplace') }}</h2>
            <p class="text-body-m text-text-muted mb-8">{{ __('Delivery to marketplace warehouses') }}</p>
            
            <div class="card max-w-2xl">
                <div class="flex justify-between items-center">
                    <div>
                        <div class="font-semibold text-body-l">{{ __('Standard box 60×40×40 cm') }}</div>
                        <div class="text-body-s text-text-muted">{{ __('Includes packaging, labeling, delivery') }}</div>
                    </div>
                    <div class="text-price text-brand">35,000 UZS</div>
                </div>
            </div>
        </div>
        
        <!-- Additional Services -->
        <div>
            <h2 class="text-h2 font-heading mb-2">{{ __('Additional Services') }}</h2>
            <p class="text-body-m text-text-muted mb-8">{{ __('Optional services to enhance your fulfillment') }}</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @php
                    $additionalServices = [
                        ['name' => __('Photo Documentation'), 'desc' => __('Photos at receiving and packing'), 'price' => __('Included')],
                        ['name' => __('Bubble Wrap Packaging'), 'desc' => __('Fragile item protection'), 'price' => '5,000 UZS'],
                        ['name' => __('Branded Packaging'), 'desc' => __('Custom boxes with your logo'), 'price' => __('By request')],
                        ['name' => __('Express Processing'), 'desc' => __('Same-day order assembly'), 'price' => '15,000 UZS'],
                    ];
                @endphp
                
                @foreach($additionalServices as $service)
                <div class="card">
                    <div class="font-semibold text-body-l mb-2">{{ $service['name'] }}</div>
                    <div class="text-body-s text-text-muted mb-4">{{ $service['desc'] }}</div>
                    <div class="text-brand font-semibold">{{ $service['price'] }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<!-- Marketplace Services -->
<section class="py-16 bg-gradient-to-br from-brand/5 to-bg-soft">
    <div class="container-risment">
        <div class="max-w-4xl mx-auto text-center mb-12">
            <h2 class="text-h2 font-heading mb-4">{{ __('Marketplace Management Services') }}</h2>
            <p class="text-body-l text-text-muted mb-6">
                {{ __('Запуск и управление вашими аккаунтами на Uzum, Wildberries, Ozon, Yandex') }}
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="card bg-white hover:shadow-lg transition">
                <div class="text-h4 font-heading mb-2">{{ __('Запуск') }}</div>
                <p class="text-body-s text-text-muted mb-4">{{ __('Настройка аккаунта и загрузка каталога') }}</p>
                <div class="text-h3 text-brand mb-1">
                    @if(app()->getLocale() === 'ru')
                        от 1 900 000
                    @else
                        1 900 000 dan
                    @endif
                </div>
                <div class="text-body-s text-text-muted">{{ __('разово') }}</div>
            </div>
            
            <div class="card bg-white border-2 border-brand hover:shadow-lg transition">
                <div class="inline-block px-3 py-1 bg-brand text-white text-body-s rounded-full mb-3">
                    {{ __('Популярно') }}
                </div>
                <div class="text-h4 font-heading mb-2">{{ __('Управление') }}</div>
                <p class="text-body-s text-text-muted mb-4">{{ __('Профессиональное ведение маркетплейса') }}</p>
                <div class="text-h3 text-brand mb-1">
                    @if(app()->getLocale() === 'ru')
                        от 1 790 000
                    @else
                        1 790 000 dan
                    @endif
                </div>
                <div class="text-body-s text-text-muted">{{ __('в месяц за маркетплейс') }}</div>
            </div>
            
            <div class="card bg-white hover:shadow-lg transition">
                <div class="text-h4 font-heading mb-2">{{ __('Инфографика') }}</div>
                <p class="text-body-s text-text-muted mb-4">{{ __('Создание продающих карточек') }}</p>
                <div class="text-h3 text-brand mb-1">60 000 / 40 000</div>
                <div class="text-body-s text-text-muted">{{ __('за товар') }}</div>
            </div>
        </div>
        
        <div class="text-center">
            <a href="{{ route('services.marketplace', ['locale' => app()->getLocale()]) }}" class="btn btn-primary mr-4">
                {{ __('Подробнее') }}
            </a>
            <a href="{{ route('calculators.marketplace', ['locale' => app()->getLocale()]) }}" class="btn btn-secondary">
                {{ __('Рассчитать стоимость') }}
            </a>
        </div>
    </div>
</section>

<!-- Bundle Discounts -->
@php
    $bundleDiscounts = \App\Models\BundleDiscount::where('type', 'management')
        ->where('is_active', true)
        ->orderBy('marketplaces_count')
        ->get();
@endphp

@if($bundleDiscounts->count() > 0)
<section class="py-12 bg-gradient-to-r from-brand/5 to-brand/10">
    <div class="container-risment">
        <div class="max-w-4xl mx-auto">
            <h2 class="text-h3 font-heading text-center mb-2">
                {{ app()->getLocale() === 'ru' ? 'Скидки при подключении нескольких маркетплейсов' : 'Bir nechta marketpleys ulashda chegirmalar' }}
            </h2>
            <p class="text-body-m text-text-muted text-center mb-8">
                {{ app()->getLocale() === 'ru' ? 'Чем больше маркетплейсов — тем выгоднее' : 'Qancha ko\'p marketpleys — shuncha foydali' }}
            </p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($bundleDiscounts as $discount)
                <div class="card bg-white text-center {{ $discount->marketplaces_count == 4 ? 'border-2 border-brand' : '' }}">
                    @if($discount->marketplaces_count == 4)
                    <div class="inline-block px-3 py-1 bg-brand text-white text-body-s rounded-full mb-3">
                        {{ app()->getLocale() === 'ru' ? 'Все 4 маркетплейса' : 'Barcha 4 marketpleys' }}
                    </div>
                    @endif
                    <div class="text-h2 font-heading text-brand mb-2">–{{ intval($discount->discount_percent) }}%</div>
                    <div class="text-body-m font-semibold mb-1">
                        {{ $discount->marketplaces_count }} {{ app()->getLocale() === 'ru' ? 'площадки' : 'ta maydon' }}
                    </div>
                    <div class="text-body-s text-text-muted">
                        {{ app()->getLocale() === 'ru' ? 'от ежемесячной абонплаты' : 'oylik abonent to\'lovidan' }}
                    </div>
                </div>
                @endforeach
            </div>
            
            <p class="text-body-s text-text-muted text-center mt-6">
                {{ app()->getLocale() === 'ru' 
                    ? '* Скидка применяется только к абонплате за управление. Разовые услуги, инфографика и рекламный бюджет не дисконтируются.' 
                    : '* Chegirma faqat boshqaruv abonent to\'loviga qo\'llaniladi. Bir martalik xizmatlar, infografika va reklama byudjeti chegirilmaydi.' }}
            </p>
        </div>
    </div>
</section>
@endif

<!-- CTA -->
<section class="py-16 bg-bg-soft">
    <div class="container-risment text-center">
        <h2 class="text-h2 font-heading mb-4">{{ __('Ready to start?') }}</h2>
        <p class="text-body-l text-text-muted mb-8">{{ __('Calculate your costs right now') }}</p>
        <div class="flex gap-4 justify-center">
            <a href="{{ route('calculator', ['locale' => app()->getLocale()]) }}" class="btn btn-primary">{{ __('Calculate Cost') }}</a>
            <a href="{{ route('contacts', ['locale' => app()->getLocale()]) }}" class="btn btn-secondary">{{ __('Contact Us') }}</a>
        </div>
    </div>
</section>
@endsection
