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
        <h2 class="text-h2 font-heading text-center mb-3">{{ __('Packages') }}</h2>
        <p class="text-body-l text-text-muted text-center mb-12 max-w-3xl mx-auto">
            {{ __('Choose a package with fixed monthly fee. Everything included: FBS processing, storage, delivery 3 times a week.') }}
        </p>
        
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6 mb-12">
            @foreach($plans as $plan)
            <div class="card bg-white {{ $plan->code === 'pro' ? 'border-2 border-brand' : '' }} relative flex flex-col" style="min-height: 400px;">
                @if($plan->code === 'pro')
                <div class="absolute -top-3 -right-3 px-4 py-2 bg-white border-2 border-brand text-brand text-body-s font-semibold rounded-btn shadow-lg">
                    {{ __('Popular') }}
                </div>
                @endif
                
                <div class="flex-grow">
                    <h3 class="text-h4 font-heading mb-2">{{ $plan->getName() }}</h3>
                    
                    @if(isset($planTaglines[$plan->code]))
                    <p class="text-body-s text-text-muted mb-4">{{ $planTaglines[$plan->code] }}</p>
                    @endif
                    
                    <div class="mb-4">
                        @if($plan->is_custom)
                            <div class="text-h3 font-heading text-brand">{{ __('from') }} {{ number_format($plan->min_price_month, 0, '', ' ') }}</div>
                            <div class="text-body-s text-text-muted">{{ __('UZS/mo') }}</div>
                            <div class="text-body-s text-brand mt-1">({{ __('individual') }})</div>
                        @else
                            <div class="text-h3 font-heading text-brand">{{ number_format($plan->price_month, 0, '', ' ') }}</div>
                            <div class="text-body-s text-text-muted">{{ __('UZS/mo') }}</div>
                        @endif
                    </div>
                    
                    <div class="space-y-2 mb-4 text-body-s">
                        @if(!$plan->is_custom)
                            @if($plan->fbs_shipments_included)
                            <div class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-brand mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span>{{ __('FBS:') }} {{ $plan->fbs_shipments_included }} {{ __('pcs') }}</span>
                            </div>
                            @endif
                            
                            @if($plan->storage_included_boxes)
                            <div class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-brand mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span>{{ __('Boxes:') }} {{ $plan->storage_included_boxes }}</span>
                            </div>
                            @endif
                            
                            @if($plan->storage_included_bags)
                            <div class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-brand mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span>{{ __('Bags:') }} {{ $plan->storage_included_bags }}</span>
                            </div>
                            @endif
                        @endif
                        
                        @if($plan->shipping_included)
                        <div class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-brand mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span>{{ __('FBS Delivery') }}</span>
                        </div>
                        @endif
                        
                        @if($plan->priority_processing)
                        <div class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-brand mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span>{{ __('Priority') }}</span>
                        </div>
                        @endif
                        
                        @if($plan->personal_manager)
                        <div class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-brand mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span>{{ __('Manager') }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Buttons pinned at bottom -->
                <div class="mt-auto pt-4">
                    @if($plan->is_custom)
                    <a href="{{ route('contacts', ['locale' => app()->getLocale()]) }}" class="btn btn-secondary w-full text-body-s py-2">
                        {{ __('Contact Us') }}
                    </a>
                    @else
                    <a href="{{ route('calculator', ['locale' => app()->getLocale()]) }}" class="btn {{ $plan->code === 'pro' ? 'btn-primary' : 'btn-secondary' }} w-full text-body-s py-2">
                        {{ __('Choose') }}
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
                <h3 class="text-h4 font-heading mb-4">{{ __('Overage charges if limit exceeded') }}</h3>
                <div class="space-y-3 text-body-s">
                    {{-- FBS Shipments Overage by Category --}}
                    <div>
                        <div class="font-semibold mb-1">{{ __('FBS shipments limit exceeded') }}:</div>
                        <ul class="ml-4 space-y-1">
                            <li>MICRO — {{ number_format($overages['shipments']['micro_fee'] ?? 0, 0, '', ' ') }} {{ __('UZS/shipment') }}</li>
                            <li>{{ __('MGT') }} — {{ number_format($overages['shipments']['mgt_fee'], 0, '', ' ') }} {{ __('UZS/shipment') }}</li>
                            <li>{{ __('SGT') }} — {{ number_format($overages['shipments']['sgt_fee'], 0, '', ' ') }} {{ __('UZS/shipment') }}</li>
                            <li>{{ __('LGT') }} — {{ number_format($overages['shipments']['kgt_fee'], 0, '', ' ') }} {{ __('UZS/shipment') }}</li>
                        </ul>
                    </div>

                    {{-- Storage Overage --}}
                    <div class="flex items-start gap-2">
                        <span class="text-brand">+</span>
                        <span>{{ __('Storage limit exceeded') }}: {{ number_format($overages['storage']['box_rate'], 0, '', ' ') }} {{ __('UZS/box/day') }}</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="text-brand">+</span>
                        <span>{{ __('Storage limit exceeded') }}: {{ number_format($overages['storage']['bag_rate'], 0, '', ' ') }} {{ __('UZS/bag/day') }}</span>
                    </div>

                    {{-- Inbound Overage --}}
                    <div class="flex items-start gap-2">
                        <span class="text-brand">+</span>
                        <span>{{ __('Receiving (inbound) limit exceeded') }}: {{ number_format($overages['inbound']['box_rate'], 0, '', ' ') }} {{ __('UZS/box') }}</span>
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
        <!-- FBS Shipment Rates by Category -->
        <div class="mb-16">
            <h2 class="text-h2 font-heading mb-2">{{ __('FBS Rates (Assembly + Delivery)') }}</h2>
            <p class="text-body-m text-text-muted mb-8">{{ __('Price depends on item dimensions (sum of L+W+H). Price includes order assembly and delivery to marketplace warehouse.') }}</p>
            
            @php
                use App\Services\PricingService;
                $pricingService = app(PricingService::class);
                $rates = $pricingService->getPublicRates();
                $dimensionCategories = config('pricing.dimension_categories');

                $categories = [
                    [
                        'code' => 'MICRO',
                        'size' => '≤' . ($dimensionCategories['micro']['max'] ?? 30) . ' cm',
                        'pickpack_first' => $rates['PICKPACK_MICRO_FIRST']['value'] ?? 2000,
                        'delivery' => $rates['DELIVERY_MICRO']['value'] ?? 2000,
                    ],
                    [
                        'code' => __('MGT'),
                        'size' => ($dimensionCategories['mgt']['min'] ?? 31) . '-' . ($dimensionCategories['mgt']['max'] ?? 60) . ' cm',
                        'pickpack_first' => $rates['PICKPACK_MGT_FIRST']['value'] ?? 4000,
                        'delivery' => $rates['DELIVERY_MGT']['value'] ?? 4000,
                    ],
                    [
                        'code' => __('SGT'),
                        'size' => ($dimensionCategories['sgt']['min'] ?? 61) . '-' . ($dimensionCategories['sgt']['max'] ?? 120) . ' cm',
                        'pickpack_first' => $rates['PICKPACK_SGT_FIRST']['value'] ?? 7000,
                        'delivery' => $rates['DELIVERY_SGT']['value'] ?? 8000,
                    ],
                    [
                        'code' => __('LGT'),
                        'size' => '>' . ($dimensionCategories['sgt']['max'] ?? 120) . ' cm',
                        'pickpack_first' => $rates['PICKPACK_KGT_FIRST']['value'] ?? 15000,
                        'delivery' => $rates['DELIVERY_KGT']['value'] ?? 20000,
                    ],
                ];
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($categories as $cat)
                <div class="card">
                    <div class="inline-block px-4 py-2 bg-brand/10 text-brand rounded-btn font-semibold mb-4">
                        {{ $cat['code'] }}
                    </div>
                    <div class="text-body-s text-text-muted mb-2">{{ __('Sum L+W+H') }}</div>
                    <div class="text-h3 font-heading mb-4">{{ $cat['size'] }}</div>
                    
                    <div class="space-y-2 text-body-s mb-4">
                        <div class="flex justify-between">
                            <span class="text-text-muted">{{ __('Assembly') }}:</span>
                            <span>{{ number_format($cat['pickpack_first'], 0, '', ' ') }} UZS</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-text-muted">{{ __('Delivery') }}:</span>
                            <span>{{ number_format($cat['delivery'], 0, '', ' ') }} UZS</span>
                        </div>
                    </div>
                    
                    <div class="border-t border-brand-border pt-3">
                        <div class="text-price text-brand">{{ number_format($cat['pickpack_first'] + $cat['delivery'], 0, '', ' ') }} UZS</div>
                        <div class="text-body-s text-text-muted mt-1">{{ __('base rate per item') }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-btn text-body-s">
                <p class="text-yellow-800">
                    💡 <strong>{{ __('Important') }}:</strong> {{ __('For one-time rate a surcharge of +10% (up to 300 shipments) or +20% (over 300) applies. Packages have no surcharge.') }}
                </p>
            </div>
        </div>
        
        <!-- Storage Rates -->
        <div class="mb-16">
            <h2 class="text-h2 font-heading mb-2">{{ __('Storage') }}</h2>
            <p class="text-body-m text-text-muted mb-8">{{ __('Storage daily rate') }}</p>
            
            @php
                $storageRates = [
                    ['type' => __('Box 60x40x40'), 'rate' => $rates['STORAGE_BOX_DAY']['value'] ?? 600],
                    ['type' => __('Clothing bag'), 'rate' => $rates['STORAGE_BAG_DAY']['value'] ?? 400],
                    ['type' => __('Pallet'), 'rate' => $rates['STORAGE_PALLET_DAY']['value'] ?? 4000],
                    ['type' => __('Cubic meter'), 'rate' => $rates['STORAGE_M3_DAY']['value'] ?? 7000],
                ];
            @endphp
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($storageRates as $storage)
                <div class="card text-center">
                    <div class="text-body-m font-semibold mb-2">{{ $storage['type'] }}</div>
                    <div class="text-price text-brand">{{ number_format($storage['rate'], 0, '', ' ') }}</div>
                    <div class="text-body-s text-text-muted mt-1">{{ __('UZS/day') }}</div>
                </div>
                @endforeach
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
                {{ __('Launch and manage your accounts on Uzum, Wildberries, Ozon, Yandex') }}
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="card bg-white hover:shadow-lg transition">
                <div class="text-h4 font-heading mb-2">{{ __('Launch') }}</div>
                <p class="text-body-s text-text-muted mb-4">{{ __('Account setup and catalog upload') }}</p>
                <div class="text-h3 text-brand mb-1">{{ __('from 1,900,000') }}</div>
                <div class="text-body-s text-text-muted">{{ __('one-time') }}</div>
            </div>
            
            <div class="card bg-white border-2 border-brand hover:shadow-lg transition">
                <div class="inline-block px-3 py-1 bg-brand text-white text-body-s rounded-full mb-3">
                    {{ __('Popular') }}
                </div>
                <div class="text-h4 font-heading mb-2">{{ __('Management') }}</div>
                <p class="text-body-s text-text-muted mb-4">{{ __('Professional marketplace management') }}</p>
                <div class="text-h3 text-brand mb-1">{{ __('from 1,790,000') }}</div>
                <div class="text-body-s text-text-muted">{{ __('per month per marketplace') }}</div>
            </div>
            
            <div class="card bg-white hover:shadow-lg transition">
                <div class="text-h4 font-heading mb-2">{{ __('Infographics') }}</div>
                <p class="text-body-s text-text-muted mb-4">{{ __('Creating product cards that sell') }}</p>
                <div class="text-h3 text-brand mb-1">60 000 / 40 000</div>
                <div class="text-body-s text-text-muted">{{ __('per product') }}</div>
            </div>
        </div>
        
        <div class="text-center">
            <a href="{{ route('services.marketplace', ['locale' => app()->getLocale()]) }}" class="btn btn-primary mr-4">
                {{ __('Learn More') }}
            </a>
            <a href="{{ route('calculators.marketplace', ['locale' => app()->getLocale()]) }}" class="btn btn-secondary">
                {{ __('Calculate Cost') }}
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
                {{ __('Discounts for connecting multiple marketplaces') }}
            </h2>
            <p class="text-body-m text-text-muted text-center mb-8">
                {{ __('The more marketplaces — the better the deal') }}
            </p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($bundleDiscounts as $discount)
                <div class="card bg-white text-center {{ $discount->marketplaces_count == 4 ? 'border-2 border-brand' : '' }}">
                    @if($discount->marketplaces_count == 4)
                    <div class="inline-block px-3 py-1 bg-brand text-white text-body-s rounded-full mb-3">
                        {{ __('All 4 marketplaces') }}
                    </div>
                    @endif
                    <div class="text-h2 font-heading text-brand mb-2">–{{ intval($discount->discount_percent) }}%</div>
                    <div class="text-body-m font-semibold mb-1">
                        {{ $discount->marketplaces_count }} {{ __('platforms') }}
                    </div>
                    <div class="text-body-s text-text-muted">
                        {{ __('of monthly management fee') }}
                    </div>
                </div>
                @endforeach
            </div>
            
            <p class="text-body-s text-text-muted text-center mt-6">
                * {{ __('Discount applies only to management subscription fee. One-time services, infographics and advertising budget are not discounted.') }}
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
