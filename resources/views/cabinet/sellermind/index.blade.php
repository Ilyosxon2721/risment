@extends('cabinet.layout')

@section('title', __('SellerMind Integration'))

@section('content')
<div class="mb-8">
    <h1 class="text-h1 font-heading">{{ __('SellerMind Integration') }}</h1>
    <p class="text-body-m text-text-muted mt-2">{{ __('Connect your SellerMind account for marketplace synchronization') }}</p>
</div>

@if($link && $link->isActive())
    <!-- Active Connection -->
    <div class="card mb-8 border-2 border-success">
        <div class="flex justify-between items-start">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-3 h-3 rounded-full bg-success animate-pulse"></div>
                    <h2 class="text-h3 font-heading text-success">{{ __('Connected') }}</h2>
                </div>
                <div class="space-y-2 text-body-m">
                    <p><span class="text-text-muted">{{ __('SellerMind Company ID') }}:</span> <strong>{{ $link->sellermind_company_id }}</strong></p>
                    <p><span class="text-text-muted">{{ __('Connected at') }}:</span> <strong>{{ $link->linked_at?->format('d.m.Y H:i') }}</strong></p>
                </div>
            </div>
            <form method="POST" action="{{ route('cabinet.sellermind.disconnect') }}" onsubmit="return confirm('{{ __('Are you sure you want to disconnect?') }}')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline text-error border-error hover:bg-error hover:text-white">
                    {{ __('Disconnect') }}
                </button>
            </form>
        </div>
    </div>

    <!-- Sync Settings -->
    <div class="card mb-8">
        <h2 class="text-h3 font-heading mb-6">{{ __('Sync Settings') }}</h2>
        <form method="POST" action="{{ route('cabinet.sellermind.settings') }}">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <label class="flex items-center gap-3 p-4 bg-bg-soft rounded-btn cursor-pointer">
                    <input type="hidden" name="sync_products" value="0">
                    <input type="checkbox" name="sync_products" value="1" {{ $link->sync_products ? 'checked' : '' }} class="w-5 h-5 text-brand rounded">
                    <div>
                        <div class="font-semibold">{{ __('Sync Products') }}</div>
                        <div class="text-body-s text-text-muted">{{ __('Push products from RISMENT to SellerMind') }}</div>
                    </div>
                </label>
                <label class="flex items-center gap-3 p-4 bg-bg-soft rounded-btn cursor-pointer">
                    <input type="hidden" name="sync_orders" value="0">
                    <input type="checkbox" name="sync_orders" value="1" {{ $link->sync_orders ? 'checked' : '' }} class="w-5 h-5 text-brand rounded">
                    <div>
                        <div class="font-semibold">{{ __('Sync Orders') }}</div>
                        <div class="text-body-s text-text-muted">{{ __('Receive marketplace orders from SellerMind') }}</div>
                    </div>
                </label>
                <label class="flex items-center gap-3 p-4 bg-bg-soft rounded-btn cursor-pointer">
                    <input type="hidden" name="sync_stock" value="0">
                    <input type="checkbox" name="sync_stock" value="1" {{ $link->sync_stock ? 'checked' : '' }} class="w-5 h-5 text-brand rounded">
                    <div>
                        <div class="font-semibold">{{ __('Sync Stock') }}</div>
                        <div class="text-body-s text-text-muted">{{ __('Bidirectional stock synchronization') }}</div>
                    </div>
                </label>
            </div>
            <div class="mt-6">
                <button type="submit" class="btn btn-primary">{{ __('Save Settings') }}</button>
            </div>
        </form>
    </div>

@elseif($link && $link->status === 'pending')
    <!-- Pending Connection -->
    <div class="card mb-8 border-2 border-warning">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-3 h-3 rounded-full bg-warning animate-pulse"></div>
            <h2 class="text-h3 font-heading text-warning">{{ __('Awaiting Connection') }}</h2>
        </div>
        <p class="text-body-m mb-6">{{ __('Enter this token in SellerMind to complete the link:') }}</p>
        <div class="p-4 bg-bg-soft rounded-btn font-mono text-lg break-all select-all border-2 border-dashed border-brand-border">
            {{ $link->link_token }}
        </div>
        <p class="text-body-s text-text-muted mt-4">
            {{ __('Go to SellerMind → Settings → Integrations → Enter RISMENT token') }}
        </p>
        <div class="mt-6">
            <form method="POST" action="{{ route('cabinet.sellermind.disconnect') }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline text-text-muted">{{ __('Cancel') }}</button>
            </form>
        </div>
    </div>

@else
    <!-- No Connection -->
    <div class="card mb-8">
        <div class="text-center py-8">
            <svg class="w-16 h-16 mx-auto mb-6 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
            </svg>
            <h2 class="text-h3 font-heading mb-2">{{ __('Not connected') }}</h2>
            <p class="text-body-m text-text-muted mb-6">
                {{ __('Link your SellerMind account to sync products, orders and stock with marketplaces.') }}
            </p>
            <form method="POST" action="{{ route('cabinet.sellermind.generate') }}">
                @csrf
                <button type="submit" class="btn btn-primary btn-lg">{{ __('Generate Link Token') }}</button>
            </form>
        </div>
    </div>

    <!-- Benefits -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="card text-center">
            <svg class="w-10 h-10 mx-auto mb-4 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <h3 class="text-h4 font-heading mb-2">{{ __('Product Sync') }}</h3>
            <p class="text-body-s text-text-muted">{{ __('Automatically push products from RISMENT to marketplaces') }}</p>
        </div>
        <div class="card text-center">
            <svg class="w-10 h-10 mx-auto mb-4 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <h3 class="text-h4 font-heading mb-2">{{ __('Order Receiving') }}</h3>
            <p class="text-body-s text-text-muted">{{ __('Get marketplace orders for fulfilment directly') }}</p>
        </div>
        <div class="card text-center">
            <svg class="w-10 h-10 mx-auto mb-4 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            <h3 class="text-h4 font-heading mb-2">{{ __('Stock Sync') }}</h3>
            <p class="text-body-s text-text-muted">{{ __('Keep stock levels in sync across systems') }}</p>
        </div>
    </div>
@endif
@endsection
