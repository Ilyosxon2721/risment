@extends('layouts.app')

@section('title', $service->{'title_' . app()->getLocale()})

@section('content')
<!-- Hero -->
<section class="gradient-brand text-white py-12">
    <div class="container-risment">
        <div class="flex items-center gap-2 mb-4">
            <a href="{{ route('services.index', ['locale' => app()->getLocale()]) }}" class="text-white/80 hover:text-white">
                {{ __('Services') }}
            </a>
            <span class="text-white/60">/</span>
            <span>{{ $service->{'title_' . app()->getLocale()} }}</span>
        </div>
        <h1 class="text-h1 font-heading">{{ $service->{'title_' . app()->getLocale()} }}</h1>
    </div>
</section>

<!-- Service Content -->
<section class="py-16">
    <div class="container-risment">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <div class="prose max-w-none">
                    {!! $service->{'content_' . app()->getLocale()} !!}
                </div>
                
                <!-- CTA -->
                <div class="mt-12 p-8 bg-bg-soft rounded-card">
                    <h3 class="text-h3 font-heading mb-4">{{ __('Ready to get started?') }}</h3>
                    <p class="text-body-m text-text-muted mb-6">
                        {{ __('Calculate your fulfillment costs or contact us for a consultation') }}
                    </p>
                    <div class="flex gap-4">
                        <a href="{{ route('calculator', ['locale' => app()->getLocale()]) }}" class="btn btn-primary">
                            {{ __('Calculate Cost') }}
                        </a>
                        <a href="{{ route('contacts', ['locale' => app()->getLocale()]) }}" class="btn btn-secondary">
                            {{ __('Contact Us') }}
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div>
                <!-- Marketplace Info -->
                <div class="card mb-6">
                    <h3 class="font-semibold mb-4">{{ __('Marketplace') }}</h3>
                    <div class="flex items-center gap-3">
                        @php
                            $marketplaceNames = [
                                'uzum' => 'Uzum Market',
                                'wb' => 'Wildberries',
                                'ozon' => 'Ozon',
                                'yandex' => 'Yandex Market'
                            ];
                        @endphp
                        <div class="w-12 h-12 bg-brand/10 rounded-full flex items-center justify-center">
                            <span class="text-brand font-bold text-xl">
                                {{ strtoupper(substr($service->marketplace, 0, 1)) }}
                            </span>
                        </div>
                        <div>
                            <div class="font-semibold">{{ $marketplaceNames[$service->marketplace] ?? $service->marketplace }}</div>
                            <div class="text-body-s text-text-muted">{{ strtoupper($service->scheme) }}</div>
                        </div>
                    </div>
                </div>
                
                <!-- Related Services -->
                <div class="card">
                    <h3 class="font-semibold mb-4">{{ __('Other Services') }}</h3>
                    @php
                        $relatedServices = \App\Models\Service::where('id', '!=', $service->id)
                            ->where('marketplace', $service->marketplace)
                            ->where('is_active', true)
                            ->get();
                    @endphp
                    
                    @forelse($relatedServices as $related)
                        <a href="{{ route('services.show', ['locale' => app()->getLocale(), 'slug' => $related->slug]) }}" 
                           class="block p-3 hover:bg-bg-soft rounded-btn transition mb-2">
                            <div class="font-semibold text-body-m">{{ $related->{'title_' . app()->getLocale()} }}</div>
                            <div class="text-body-s text-text-muted">{{ strtoupper($related->scheme) }}</div>
                        </a>
                    @empty
                        <p class="text-body-s text-text-muted">{{ __('No related services') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>

<!-- All Services -->
<section class="py-16 bg-bg-soft">
    <div class="container-risment">
        <h2 class="text-h2 font-heading text-center mb-8">{{ __('All Marketplaces') }}</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @php
                $marketplaces = [
                    ['name' => 'Uzum Market', 'slug' => 'uzum'],
                    ['name' => 'Wildberries', 'slug' => 'wb'],
                    ['name' => 'Ozon', 'slug' => 'ozon'],
                    ['name' => 'Yandex Market', 'slug' => 'yandex'],
                ];
            @endphp
            
            @foreach($marketplaces as $mp)
                <a href="{{ route('services.index', ['locale' => app()->getLocale(), 'marketplace' => $mp['slug']]) }}" 
                   class="card text-center hover:shadow-lg transition">
                    <div class="w-16 h-16 bg-brand/10 rounded-full flex items-center justify-center mx-auto mb-3">
                        <span class="text-brand font-bold text-2xl">{{ strtoupper(substr($mp['slug'], 0, 1)) }}</span>
                    </div>
                    <div class="font-semibold">{{ $mp['name'] }}</div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endsection

<style>
.prose h3 {
    @apply text-h3 font-heading mt-8 mb-4;
}

.prose h4 {
    @apply text-h4 font-heading mt-6 mb-3;
}

.prose p {
    @apply text-body-m mb-4;
}

.prose ul {
    @apply list-disc list-inside mb-4 space-y-2;
}

.prose li {
    @apply text-body-m;
}
</style>
