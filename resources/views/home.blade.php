@extends('layouts.app')

@section('title', __('Professional Fulfillment for Marketplaces') . ' - RISMENT')

@section('content')
<!-- Hero Section -->
<section class="gradient-brand text-white py-20">
    <div class="container-risment">
        <div class="max-w-3xl">
            <h1 class="text-h1 font-heading mb-6">
                {{ __('Fulfillment for Uzbekistan Marketplaces') }}
            </h1>
            <p class="text-body-l mb-8 opacity-90">
                {{ __('Professional storage, packaging and delivery for Uzum, Wildberries, Ozon, Yandex Market') }}
            </p>
            <div class="flex gap-4">
                <a href="{{ route('calculator', ['locale' => app()->getLocale()]) }}" class="btn btn-primary bg-white text-brand hover:bg-gray-100">
                    {{ __('Calculate') }}
                </a>
                <a href="{{ route('contacts', ['locale' => app()->getLocale()]) }}" class="btn btn-secondary border-white text-white hover:bg-white/10">
                    {{ __('Leave Request') }}
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Why RISMENT -->
<section class="py-16">
    <div class="container-risment">
        <h2 class="text-h2 font-heading text-center mb-12">{{ __('Why RISMENT') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="card text-center">
                <div class="w-16 h-16 rounded-full gradient-brand flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-h4 font-heading mb-3">{{ __('SLA Guarantees') }}</h3>
                <p class="text-body-s text-text-muted">{{ __('Transparent timelines for every process') }}</p>
            </div>
            
            <div class="card text-center">
                <div class="w-16 h-16 rounded-full gradient-brand flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h3 class="text-h4 font-heading mb-3">{{ __('Personal Dashboard') }}</h3>
                <p class="text-body-s text-text-muted">{{ __('Online 24/7 monitoring of inventory and shipments') }}</p>
            </div>
            
            <div class="card text-center">
                <div class="w-16 h-16 rounded-full gradient-brand flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <h3 class="text-h4 font-heading mb-3">{{ __('Photo Documentation') }}</h3>
                <p class="text-body-s text-text-muted">{{ __('Photos of every receiving and shipment') }}</p>
            </div>
        </div>
    </div>
</section>

<!-- Our Services -->
<section class="py-16 bg-bg-soft">
    <div class="container-risment">
    <h2 class="text-h2 font-heading text-center mb-12">{{ __('Our Services') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="card hover:shadow-lg transition">
                <div class="text-4xl mb-4">📦</div>
                <h3 class="text-h4 font-heading mb-2">{{ __('FBS Service') }}</h3>
                <p class="text-body-s text-text-muted">{{ __('We store, pack and ship your orders to marketplace') }}</p>
            </div>
            <div class="card hover:shadow-lg transition">
                <div class="text-4xl mb-4">🏠</div>
                <h3 class="text-h4 font-heading mb-2">{{ __('Warehouse Storage') }}</h3>
                <p class="text-body-s text-text-muted">{{ __('Safe storage with climate control and 24/7 security') }}</p>
            </div>
            <div class="card hover:shadow-lg transition">
                <div class="text-4xl mb-4">🚚</div>
                <h3 class="text-h4 font-heading mb-2">{{ __('Delivery to Marketplace') }}</h3>
                <p class="text-body-s text-text-muted">{{ __('Fast delivery to marketplace warehouses') }}</p>
            </div>
            <div class="card hover:shadow-lg transition">
                <div class="text-4xl mb-4">📸</div>
                <h3 class="text-h4 font-heading mb-2">{{ __('Photo Documentation') }}</h3>
                <p class="text-body-s text-text-muted">{{ __('Photo of every receiving and shipment for full transparency') }}</p>
            </div>
            <div class="card hover:shadow-lg transition">
                <div class="text-4xl mb-4">📊</div>
                <h3 class="text-h4 font-heading mb-2">{{ __('Analytics & Reporting') }}</h3>
                <p class="text-body-s text-text-muted">{{ __('Real-time inventory and shipment tracking in your dashboard') }}</p>
            </div>
            <div class="card hover:shadow-lg transition">
                <div class="text-4xl mb-4">🔄</div>
                <h3 class="text-h4 font-heading mb-2">{{ __('Returns Processing') }}</h3>
                <p class="text-body-s text-text-muted">{{ __('Quick and accurate processing of marketplace returns') }}</p>
            </div>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="py-16">
    <div class="container-risment">
    <h2 class="text-h2 font-heading text-center mb-12">{{ __('How It Works') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="w-20 h-20 rounded-full bg-brand/10 text-brand flex items-center justify-center mx-auto mb-4 font-bold text-h2">1</div>
                <h3 class="text-h4 font-heading mb-2">{{ __('Application') }}</h3>
                <p class="text-body-s text-text-muted">{{ __('Leave a request or call us') }}</p>
            </div>
            <div class="text-center">
                <div class="w-20 h-20 rounded-full bg-brand/10 text-brand flex items-center justify-center mx-auto mb-4 font-bold text-h2">2</div>
                <h3 class="text-h4 font-heading mb-2">{{ __('Agreement') }}</h3>
                <p class="text-body-s text-text-muted">{{ __('We sign the contract and set up dashboard access') }}</p>
            </div>
            <div class="text-center">
                <div class="w-20 h-20 rounded-full bg-brand/10 text-brand flex items-center justify-center mx-auto mb-4 font-bold text-h2">3</div>
                <h3 class="text-h4 font-heading mb-2">{{ __('Goods Delivery') }}</h3>
                <p class="text-body-s text-text-muted">{{ __('You deliver goods to our warehouse') }}</p>
            </div>
            <div class="text-center">
                <div class="w-20 h-20 rounded-full bg-brand/10 text-brand flex items-center justify-center mx-auto mb-4 font-bold text-h2">4</div>
                <h3 class="text-h4 font-heading mb-2">{{ __('Fulfillment') }}</h3>
                <p class="text-body-s text-text-muted">{{ __('We store, pack and deliver your orders') }}</p>
            </div>
        </div>
    </div>
</section>

<!-- Company Stats -->
<section class="py-16 bg-gradient-to-br from-brand/5 to-bg-soft">
    <div class="container-risment">
    <h2 class="text-h2 font-heading text-center mb-12">{{ __('Company in Numbers') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="card text-center bg-white">
                <div class="text-h1 text-brand mb-2">10K+</div>
                <div class="text-body-m text-text-muted">{{ __('Orders processed') }}</div>
            </div>
            <div class="card text-center bg-white">
                <div class="text-h1 text-success mb-2">99%</div>
                <div class="text-body-m text-text-muted">{{ __('SLA compliance') }}</div>
            </div>
            <div class="card text-center bg-white">
                <div class="text-h1 text-brand mb-2">24/7</div>
                <div class="text-body-m text-text-muted">{{ __('Support available') }}</div>
            </div>
            <div class="card text-center bg-white">
                <div class="text-h1 text-brand mb-2">5K+</div>
                <div class="text-body-m text-text-muted">{{ __('m² warehouse space') }}</div>
            </div>
        </div>
    </div>
</section>

<!-- Marketplaces -->
<section class="py-16 bg-bg-soft">
    <div class="container-risment">
    <h2 class="text-h2 font-heading text-center mb-12">{{ __('We work with') }}</h2>
        <div class="flex flex-wrap justify-center items-center gap-12">
            <img src="{{ asset('images/logos/uzum.png') }}" alt="Uzum" class="h-16 object-contain">
            <img src="{{ asset('images/logos/wildberries.svg') }}" alt="Wildberries" class="h-16 object-contain">
            <img src="{{ asset('images/logos/ozon.svg') }}" alt="Ozon" class="h-16 object-contain">
            <img src="{{ asset('images/logos/yandex.svg') }}" alt="Yandex Market" class="h-16 object-contain">
        </div>
    </div>
</section>

<!-- Warehouse Info -->
<section class="py-16">
    <div class="container-risment">
    <h2 class="text-h2 font-heading text-center mb-12">{{ __('Our Warehouse') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
            <div>
                <div class="aspect-video bg-gradient-to-br from-brand/20 to-brand/5 rounded-btn flex items-center justify-center text-brand">
                    <svg class="w-32 h-32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
            <div>
                <h3 class="text-h3 font-heading mb-4">{{ __('Modern Warehouse in Tashkent') }}</h3>
                <p class="text-body-m text-text-muted mb-6">{{ __('Tashkent, Uzbekistan') }}</p>
                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-brand flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-body-s">{{ __('5,000+ m² of storage space') }}</span>
                    </div>
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-brand flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-body-s">{{ __('Temperature and humidity control') }}</span>
                    </div>
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-brand flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-body-s">{{ __('24/7 security system') }}</span>
                    </div>
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-brand flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-body-s">{{ __('Modern WMS system') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="py-16 bg-bg-soft">
    <div class="container-risment">
    <h2 class="text-h2 font-heading text-center mb-12">{{ __('Client Reviews') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="card bg-white">
                <div class="flex gap-1 mb-3">
                    @for($i = 0; $i < 5; $i++)
                    <svg class="w-5 h-5 text-warning" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    @endfor
                </div>
                <p class="text-body-s mb-4">{{ __('Excellent service! We transferred all our logistics to RISMENT and have not regretted it.') }}</p>
                <div class="border-t border-brand-border pt-3">
                    <div class="font-semibold">{{ __('Akbar Karimov') }}</div>
                    <div class="text-body-s text-text-muted">{{ __('Electronics') }}</div>
                </div>
            </div>
            <div class="card bg-white">
                <div class="flex gap-1 mb-3">
                    @for($i = 0; $i < 5; $i++)
                    <svg class="w-5 h-5 text-warning" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    @endfor
                </div>
                <p class="text-body-s mb-4">{{ __('Fast and high quality. SLA is always met, the dashboard is very convenient.') }}</p>
                <div class="border-t border-brand-border pt-3">
                    <div class="font-semibold">{{ __('Maria Ivanova') }}</div>
                    <div class="text-body-s text-text-muted">{{ __('Clothing') }}</div>
                </div>
            </div>
            <div class="card bg-white">
                <div class="flex gap-1 mb-3">
                    @for($i = 0; $i < 5; $i++)
                    <svg class="w-5 h-5 text-warning" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    @endfor
                </div>
                <p class="text-body-s mb-4">{{ __('Working with RISMENT since 2023. The best fulfillment in Uzbekistan.') }}</p>
                <div class="border-t border-brand-border pt-3">
                    <div class="font-semibold">{{ __('Bobur Rakhimov') }}</div>
                    <div class="text-body-s text-text-muted">{{ __('Cosmetics') }}</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ -->
<section class="py-16">
    <div class="container-risment max-w-3xl">
        <h2 class="text-h2 font-heading text-center mb-12">{{ __('FAQ') }}</h2>
        <div class="space-y-4">
            <details class="card group">
                <summary class="cursor-pointer font-semibold text-body-l flex justify-between items-center">
                    {{ __('What is fulfillment?') }}
                    <svg class="w-5 h-5 text-brand transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </summary>
                <p class="text-body-s text-text-muted mt-3 pt-3 border-t border-brand-border">{{ __('Fulfillment is a comprehensive service including storage, packaging and delivery of goods for online stores and marketplace sellers.') }}</p>
            </details>
            <details class="card group">
                <summary class="cursor-pointer font-semibold text-body-l flex justify-between items-center">
                    {{ __('What marketplaces do you work with?') }}
                    <svg class="w-5 h-5 text-brand transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </summary>
                <p class="text-body-s text-text-muted mt-3 pt-3 border-t border-brand-border">{{ __('We work with Uzum, Wildberries, Ozon and Yandex Market.') }}</p>
            </details>
            <details class="card group">
                <summary class="cursor-pointer font-semibold text-body-l flex justify-between items-center">
                    {{ __('What is FBS?') }}
                    <svg class="w-5 h-5 text-brand transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </summary>
                <p class="text-body-s text-text-muted mt-3 pt-3 border-t border-brand-border">{{ __('FBS (Fulfillment by Seller) means the seller stores goods in our warehouse, and we handle assembly and delivery to the marketplace.') }}</p>
            </details>
            <details class="card group">
                <summary class="cursor-pointer font-semibold text-body-l flex justify-between items-center">
                    {{ __('How quickly do you process orders?') }}
                    <svg class="w-5 h-5 text-brand transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </summary>
                <p class="text-body-s text-text-muted mt-3 pt-3 border-t border-brand-border">{{ __('Orders are assembled on the same day. Delivery to the marketplace warehouse takes 1-2 business days.') }}</p>
            </details>
            <details class="card group">
                <summary class="cursor-pointer font-semibold text-body-l flex justify-between items-center">
                    {{ __('How do I start working with you?') }}
                    <svg class="w-5 h-5 text-brand transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </summary>
                <p class="text-body-s text-text-muted mt-3 pt-3 border-t border-brand-border">{{ __('Leave a request on our website or call us. We will discuss terms, sign the contract and you can start delivering goods to our warehouse.') }}</p>
            </details>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-16 bg-bg-soft">
    <div class="container-risment">
        <div class="card gradient-brand text-white text-center p-12">
            <h2 class="text-h2 font-heading mb-4">{{ __('Ready to start?') }}</h2>
            <p class="text-body-l mb-6 opacity-90">{{ __('Calculate the cost or contact us') }}</p>
            <div class="flex gap-4 justify-center">
                <a href="{{ route('calculator', ['locale' => app()->getLocale()]) }}" class="btn btn-primary bg-white text-brand hover:bg-gray-100">
                    {{ __('Calculate') }}
                </a>
                <a href="{{ route('contacts', ['locale' => app()->getLocale()]) }}" class="btn btn-secondary border-white text-white hover:bg-white/10">
                    {{ __('Contacts') }}
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
