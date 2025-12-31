@extends('layouts.app')

@section('title', __('Services') . ' - RISMENT')

@section('content')
<section class="py-16">
    <div class="container-risment">
        <h1 class="text-h1 font-heading text-center mb-12">{{ __('Services') }}</h1>
        
        <!-- Filters -->
        <div class="flex flex-wrap gap-4 justify-center mb-12">
            <div class="flex gap-2">
                <a href="{{ route('services.index', ['locale' => app()->getLocale()]) }}" 
                   class="btn {{ !request('marketplace') ? 'btn-primary' : 'btn-secondary' }}">
                    {{ __('All') }}
                </a>
                <a href="{{ route('services.index', ['locale' => app()->getLocale(), 'marketplace' => 'uzum']) }}" 
                   class="btn {{ request('marketplace') === 'uzum' ? 'btn-primary' : 'btn-secondary' }}">
                    Uzum
                </a>
                <a href="{{ route('services.index', ['locale' => app()->getLocale(), 'marketplace' => 'wb']) }}" 
                   class="btn {{ request('marketplace') === 'wb' ? 'btn-primary' : 'btn-secondary' }}">
                    Wildberries
                </a>
                <a href="{{ route('services.index', ['locale' => app()->getLocale(), 'marketplace' => 'ozon']) }}" 
                   class="btn {{ request('marketplace') === 'ozon' ? 'btn-primary' : 'btn-secondary' }}">
                    Ozon
                </a>
                <a href="{{ route('services.index', ['locale' => app()->getLocale(), 'marketplace' => 'yandex']) }}" 
                   class="btn {{ request('marketplace') === 'yandex' ? 'btn-primary' : 'btn-secondary' }}">
                    Yandex
                </a>
            </div>
        </div>
        
        <!-- Services Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @forelse($services as $service)
            <div class="card">
                <div class="mb-4">
                    <span class="badge badge-info">{{ strtoupper($service->scheme) }}</span>
                    <span class="badge badge-success ml-2">{{ strtoupper($service->marketplace) }}</span>
                </div>
                <h3 class="text-h4 font-heading mb-3">
                    {{ app()->getLocale() === 'ru' ? $service->title_ru : $service->title_uz }}
                </h3>
                <p class="text-body-s text-text-muted mb-4">
                    {{ Str::limit(strip_tags(app()->getLocale() === 'ru' ? $service->content_ru : $service->content_uz), 150) }}
                </p>
                <a href="{{ route('services.show', ['locale' => app()->getLocale(), 'slug' => $service->slug]) }}" 
                   class="btn btn-secondary w-full">
                    {{ __('Learn more') }}
                </a>
            </div>
            @empty
            <div class="col-span-3 text-center py-12">
                <p class="text-body-l text-text-muted">
                    @if(app()->getLocale() === 'ru')
                        Услуги не найдены
                    @else
                        Xizmatlar topilmadi
                    @endif
                </p>
            </div>
            @endforelse
        </div>
    </div>
</section>
@endsection
