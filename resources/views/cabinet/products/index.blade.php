@extends('cabinet.layout')

@section('title', __('Products'))

@section('content')
<div class="p-6">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-h2 font-heading">{{ __('Products') }}</h1>
            <p class="text-body-s text-text-muted mt-1">{{ __('Manage your products with variants') }}</p>
        </div>
        <a href="{{ route('cabinet.products.create') }}" class="btn btn-primary">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            {{ __('Create Product') }}
        </a>
    </div>

    {{-- Search & Filters --}}
    <div class="card mb-6">
        <form method="GET" class="flex gap-4">
            <div class="flex-1">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="{{ __('Search by title or article...') }}"
                    class="input">
            </div>
            <button type="submit" class="btn btn-secondary">
                {{ __('Search') }}
            </button>
            @if(request('search'))
                <a href="{{ route('cabinet.products.index') }}" class="btn btn-ghost">
                    {{ __('Clear') }}
                </a>
            @endif
        </form>
    </div>

    {{-- Products Table --}}
    <div class="card" x-data="{ expandedProducts: [] }">
        @if($products->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-bg-soft border-b-2 border-brand-border">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">{{ __('Product') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">{{ __('Article') }}</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider">{{ __('Variants') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">{{ __('Status') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">SellerMind</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">{{ __('Created') }}</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-brand-border">
                        @foreach($products as $product)
                        <tr class="hover:bg-bg-soft transition">
                            <td class="px-4 py-4">
                                <button 
                                    @click="expandedProducts.includes({{ $product->id }}) ? expandedProducts = expandedProducts.filter(id => id !== {{ $product->id }}) : expandedProducts.push({{ $product->id }})"
                                    class="flex items-center gap-2 font-semibold text-brand hover:text-brand-hover">
                                    <svg class="w-4 h-4 transition-transform" :class="{'rotate-90': expandedProducts.includes({{ $product->id }})}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                    {{ $product->title }}
                                </button>
                            </td>
                            <td class="px-4 py-4 text-body-s text-text-muted">{{ $product->article }}</td>
                            <td class="px-4 py-4 text-center">
                                <span class="badge badge-info">{{ $product->variants_count }}</span>
                            </td>
                            <td class="px-4 py-4">
                                @if($product->is_active)
                                    <span class="badge badge-success">{{ __('Active') }}</span>
                                @else
                                    <span class="badge badge-error">{{ __('Inactive') }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                @if($product->sellermind_sync_status === 'synced')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        {{ __('Synced') }}
                                    </span>
                                @elseif($product->sellermind_sync_status === 'error')
                                    <div x-data="{ showError: false }" class="relative">
                                        <button @click="showError = !showError" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 cursor-pointer hover:bg-red-200">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                            {{ __('Error') }}
                                        </button>
                                        <div x-show="showError" @click.away="showError = false" x-cloak
                                             class="absolute z-50 mt-1 left-0 w-72 bg-white rounded-lg shadow-lg border border-red-200 p-3">
                                            <p class="text-xs text-gray-700 mb-2">{{ $product->sellermind_sync_error }}</p>
                                            <form action="{{ route('cabinet.products.resync', $product) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                                    {{ __('Retry sync') }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <svg class="w-3 h-3 mr-1 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                        {{ __('Pending') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-body-s text-text-muted">{{ $product->created_at->format('d.m.Y') }}</td>
                            <td class="px-4 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('cabinet.products.edit', $product) }}" class="btn btn-sm btn-secondary">
                                        {{ __('Edit') }}
                                    </a>
                                    <form method="POST" action="{{ route('cabinet.products.destroy', $product) }}" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-error">
                                            {{ __('Delete') }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        
                        {{-- Expanded Variants --}}
                        <tr x-show="expandedProducts.includes({{ $product->id }})" x-cloak>
                            <td colspan="7" class="px-4 py-4 bg-gray-50">
                                <div class="space-y-2">
                                    <div class="text-xs font-semibold uppercase text-text-muted mb-2">{{ __('Variants') }}:</div>
                                    @foreach($product->variants as $variant)
                                        <div class="flex items-start gap-4 p-3 bg-white rounded-lg border border-brand-border">
                                            {{-- Primary Image --}}
                                            @if($variant->images->where('is_primary', true)->first())
                                                <img src="{{ asset('storage/' . $variant->images->where('is_primary', true)->first()->image_path) }}" 
                                                     alt="{{ $variant->variant_name }}"
                                                     class="w-16 h-16 object-cover rounded">
                                            @else
                                                <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                            
                                            {{-- Variant Info --}}
                                            <div class="flex-1">
                                                <div class="font-semibold">{{ $variant->variant_name }}</div>
                                                <div class="text-xs text-text-muted mt-1">
                                                    <span>SKU: {{ $variant->sku_code }}</span>
                                                    @if($variant->barcode)
                                                        <span class="ml-3">{{ __('Barcode') }}: {{ $variant->barcode }}</span>
                                                    @endif
                                                </div>
                                                
                                                {{-- Attributes --}}
                                                @if($variant->attributes->count() > 0)
                                                    <div class="flex gap-2 mt-2">
                                                        @foreach($variant->attributes as $attr)
                                                            <span class="badge badge-ghost">{{ $attr->attribute_name }}: {{ $attr->attribute_value }}</span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                                
                                                {{-- Marketplace Links --}}
                                                @if($variant->marketplaceLinks->count() > 0)
                                                    <div class="flex gap-2 mt-2">
                                                        @foreach($variant->marketplaceLinks as $link)
                                                            <span class="text-xs text-brand">{{ ucfirst($link->marketplace) }}</span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            {{-- Dimensions --}}
                                            @if($variant->dims_l || $variant->dims_w || $variant->dims_h)
                                                <div class="text-right text-xs text-text-muted">
                                                    <div>{{ __('Dimensions') }}:</div>
                                                    <div class="font-semibold">{{ $variant->dims_l }}×{{ $variant->dims_w }}×{{ $variant->dims_h }} cm</div>
                                                    @if($variant->weight)
                                                        <div class="mt-1">{{ $variant->weight }} {{ __('kg') }}</div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            {{-- Pagination --}}
            @if($products->hasPages())
                <div class="px-4 py-3 border-t border-brand-border">
                    {{ $products->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <h3 class="text-h4 font-heading mb-2">{{ __('No products yet') }}</h3>
                <p class="text-body-s text-text-muted mb-4">{{ __('Create your first product to get started') }}</p>
                <a href="{{ route('cabinet.products.create') }}" class="btn btn-primary">
                    {{ __('Create Product') }}
                </a>
            </div>
        @endif
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
