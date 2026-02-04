@extends('cabinet.layout')

@section('title', $credential ? __('marketplaces.edit_title') : __('marketplaces.create_title'))

@section('content')
<div class="mb-6">
    <a href="{{ route('cabinet.marketplaces.index') }}" class="inline-flex items-center gap-2 text-body-m text-text-muted hover:text-brand transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        {{ __('marketplaces.back') }}
    </a>
</div>

<div class="mb-8">
    <h1 class="text-h1 font-heading">
        {{ $credential ? __('marketplaces.edit_title') : __('marketplaces.create_title') }}
        — {{ __('marketplaces.mp_' . $marketplace) }}
    </h1>
</div>

<form method="POST" action="{{ $credential ? route('cabinet.marketplaces.update', $credential) : route('cabinet.marketplaces.store') }}" class="card max-w-2xl">
    @csrf
    @if($credential) @method('PUT') @endif
    <input type="hidden" name="marketplace" value="{{ $marketplace }}">

    <div class="space-y-6">
        {{-- Name --}}
        <div>
            <label class="block font-semibold mb-2">{{ __('marketplaces.account_name') }} *</label>
            <input type="text" name="name" value="{{ old('name', $credential?->name) }}" required class="input w-full" placeholder="{{ __('marketplaces.name_placeholder') }}">
            @error('name') <p class="text-error text-body-s mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Marketplace-specific fields --}}
        @if($marketplace === 'wildberries')
            <div>
                <label class="block font-semibold mb-2">API {{ __('marketplaces.token') }} *</label>
                <textarea name="wb_api_token" rows="3" required class="input w-full font-mono text-sm">{{ old('wb_api_token', $credential?->wb_api_token) }}</textarea>
                @error('wb_api_token') <p class="text-error text-body-s mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block font-semibold mb-2">Supplier ID</label>
                <input type="text" name="wb_supplier_id" value="{{ old('wb_supplier_id', $credential?->wb_supplier_id) }}" class="input w-full">
                @error('wb_supplier_id') <p class="text-error text-body-s mt-1">{{ $message }}</p> @enderror
            </div>
        @elseif($marketplace === 'ozon')
            <div>
                <label class="block font-semibold mb-2">Client ID *</label>
                <input type="text" name="ozon_client_id" value="{{ old('ozon_client_id', $credential?->ozon_client_id) }}" required class="input w-full">
                @error('ozon_client_id') <p class="text-error text-body-s mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block font-semibold mb-2">API Key *</label>
                <textarea name="ozon_api_key" rows="3" required class="input w-full font-mono text-sm">{{ old('ozon_api_key', $credential?->ozon_api_key) }}</textarea>
                @error('ozon_api_key') <p class="text-error text-body-s mt-1">{{ $message }}</p> @enderror
            </div>
        @elseif($marketplace === 'uzum')
            <div>
                <label class="block font-semibold mb-2">API {{ __('marketplaces.token') }} *</label>
                <textarea name="uzum_api_token" rows="3" required class="input w-full font-mono text-sm">{{ old('uzum_api_token', $credential?->uzum_api_token) }}</textarea>
                @error('uzum_api_token') <p class="text-error text-body-s mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block font-semibold mb-2">Seller ID</label>
                <input type="text" name="uzum_seller_id" value="{{ old('uzum_seller_id', $credential?->uzum_seller_id) }}" class="input w-full">
                @error('uzum_seller_id') <p class="text-error text-body-s mt-1">{{ $message }}</p> @enderror
            </div>
        @elseif($marketplace === 'yandex_market')
            <div>
                <label class="block font-semibold mb-2">OAuth {{ __('marketplaces.token') }} *</label>
                <textarea name="yandex_oauth_token" rows="3" required class="input w-full font-mono text-sm">{{ old('yandex_oauth_token', $credential?->yandex_oauth_token) }}</textarea>
                @error('yandex_oauth_token') <p class="text-error text-body-s mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block font-semibold mb-2">Campaign ID</label>
                <input type="text" name="yandex_campaign_id" value="{{ old('yandex_campaign_id', $credential?->yandex_campaign_id) }}" class="input w-full">
                @error('yandex_campaign_id') <p class="text-error text-body-s mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block font-semibold mb-2">Business ID</label>
                <input type="text" name="yandex_business_id" value="{{ old('yandex_business_id', $credential?->yandex_business_id) }}" class="input w-full">
                @error('yandex_business_id') <p class="text-error text-body-s mt-1">{{ $message }}</p> @enderror
            </div>
        @endif

        {{-- Active toggle --}}
        <label class="flex items-center gap-3 cursor-pointer">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $credential?->is_active ?? true) ? 'checked' : '' }} class="w-5 h-5 text-brand rounded">
            <span class="font-semibold">{{ __('marketplaces.active') }}</span>
        </label>
    </div>

    <div class="mt-8 flex gap-4">
        <button type="submit" class="btn btn-primary">{{ $credential ? __('marketplaces.save') : __('marketplaces.create') }}</button>
        <a href="{{ route('cabinet.marketplaces.index') }}" class="btn btn-outline">{{ __('marketplaces.cancel') }}</a>
    </div>
</form>
@endsection
