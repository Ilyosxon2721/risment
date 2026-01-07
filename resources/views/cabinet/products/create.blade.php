@extends('cabinet.layout')

@section('title', __('Create Product'))

@section('content')
<div class="p-6" x-data="productForm()">
    {{-- Header --}}
    <div class="mb-6">
        <a href="{{ route('cabinet.products.index') }}" class="text-brand hover:text-brand-hover inline-flex items-center mb-4">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            {{ __('Back to products') }}
        </a>
        <h1 class="text-h2 font-heading">{{ __('Create Product') }}</h1>
    </div>

    <form method="POST" action="{{ route('cabinet.products.store') }}" enctype="multipart/form-data">
        @csrf

        {{-- Product Info Card --}}
        <div class="card mb-6">
            <h2 class="text-h3 font-heading mb-4">{{ __('Product Information') }}</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Title --}}
                <div class="md:col-span-2">
                    <label class="label" for="title">
                        {{ __('Product Title') }} <span class="text-error">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="title" 
                        id="title"
                        value="{{ old('title') }}"
                        class="input @error('title') border-error @enderror"
                        required>
                    @error('title')
                        <p class="text-error text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Short Description --}}
                <div class="md:col-span-2">
                    <label class="label" for="short_description">
                        {{ __('Short Description') }}
                    </label>
                    <textarea 
                        name="short_description" 
                        id="short_description"
                        rows="2"
                        class="input"
                        placeholder="{{ __('Brief description for listings (max 500 chars)') }}"
                        maxlength="500">{{ old('short_description') }}</textarea>
                    <p class="text-xs text-text-muted mt-1">{{ __('Used for product cards and previews') }}</p>
                </div>

                {{-- Article --}}
                <div>
                    <label class="label" for="article">
                        {{ __('Article') }} <span class="text-error">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="article" 
                        id="article"
                        value="{{ old('article') }}"
                        class="input @error('article') border-error @enderror"
                        required>
                    @error('article')
                        <p class="text-error text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Active Status --}}
                <div>
                    <label class="label">{{ __('Status') }}</label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input 
                            type="checkbox" 
                            name="is_active" 
                            value="1"
                            checked
                            class="checkbox">
                        <span>{{ __('Active') }}</span>
                    </label>
                </div>

                {{-- Description --}}
                <div class="md:col-span-2">
                    <label class="label" for="description">{{ __('Description') }}</label>
                    <textarea 
                        name="description" 
                        id="description"
                        rows="3"
                        class="input">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Variants --}}
        <div class="card mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-h3 font-heading">{{ __('Product Variants') }}</h2>
                <button 
                    type="button"
                    @click="addVariant()"
                    class="btn btn-secondary btn-sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    {{ __('Add Variant') }}
                </button>
            </div>

            <div class="space-y-6">
                <template x-for="(variant, vIndex) in variants" :key="vIndex">
                    <div class="p-4 border-2 border-brand-border rounded-lg">
                        {{-- Variant Header --}}
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-semibold text-brand" x-text="'{{ __('Variant') }} ' + (vIndex + 1)"></h3>
                            <button 
                                type="button"
                                @click="removeVariant(vIndex)"
                                x-show="variants.length > 1"
                                class="text-error hover:text-error-hover">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Variant Name --}}
                            <div class="md:col-span-2">
                                <label class="label">
                                    {{ __('Variant Name') }} <span class="text-error">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    :name="'variants[' + vIndex + '][variant_name]'"
                                    x-model="variant.variant_name"
                                    class="input"
                                    placeholder="{{ __('e.g. Red, XL') }}"
                                    required>
                            </div>

                            {{-- SKU Code --}}
                            <div>
                                <label class="label">
                                    {{ __('SKU Code') }} <span class="text-error">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    :name="'variants[' + vIndex + '][sku_code]'"
                                    x-model="variant.sku_code"
                                    class="input"
                                    required>
                            </div>

                            {{-- Barcode --}}
                            <div>
                                <label class="label">{{ __('Barcode') }}</label>
                                <input 
                                    type="text" 
                                    :name="'variants[' + vIndex + '][barcode]'"
                                    x-model="variant.barcode"
                                    class="input">
                            </div>

                            {{-- Dimensions --}}
                            <div class="md:col-span-2">
                                <label class="label">{{ __('Dimensions (L × W × H, cm)') }}</label>
                                <div class="grid grid-cols-3 gap-2">
                                    <input 
                                        type="number" 
                                        :name="'variants[' + vIndex + '][dims_l]'"
                                        x-model="variant.dims_l"
                                        placeholder="{{ __('Length') }}"
                                        step="0.1"
                                        class="input">
                                    <input 
                                        type="number" 
                                        :name="'variants[' + vIndex + '][dims_w]'"
                                        x-model="variant.dims_w"
                                        placeholder="{{ __('Width') }}"
                                        step="0.1"
                                        class="input">
                                    <input 
                                        type="number" 
                                        :name="'variants[' + vIndex + '][dims_h]'"
                                        x-model="variant.dims_h"
                                        placeholder="{{ __('Height') }}"
                                        step="0.1"
                                        class="input">
                                </div>
                            </div>

                            {{-- Weight --}}
                            <div>
                                <label class="label">{{ __('Weight (kg)') }}</label>
                                <input 
                                    type="number" 
                                    :name="'variants[' + vIndex + '][weight]'"
                                    x-model="variant.weight"
                                    step="0.01"
                                    class="input">
                            </div>

                            {{-- Price --}}
                            <div>
                                <label class="label">{{ __('Price') }}</label>
                                <input 
                                    type="number" 
                                    :name="'variants[' + vIndex + '][price]'"
                                    x-model="variant.price"
                                    step="0.01"
                                    placeholder="0.00"
                                    class="input">
                            </div>

                            {{-- Cost Price --}}
                            <div>
                                <label class="label">{{ __('Cost Price') }}</label>
                                <input 
                                    type="number" 
                                    :name="'variants[' + vIndex + '][cost_price]'"
                                    x-model="variant.cost_price"
                                    step="0.01"
                                    placeholder="0.00"
                                    class="input">
                            </div>

                            {{-- Expenses --}}
                            <div>
                                <label class="label">{{ __('Expenses') }}</label>
                                <input 
                                    type="number" 
                                    :name="'variants[' + vIndex + '][expenses]'"
                                    x-model="variant.expenses"
                                    step="0.01"
                                    placeholder="0.00"
                                    class="input">
                            </div>

                            {{-- Images Upload --}}
                            <div class="md:col-span-2">
                                <label class="label">{{ __('Images') }} ({{ __('max 10') }})</label>
                                <input 
                                    type="file" 
                                    :name="'variants[' + vIndex + '][images][]'"
                                    multiple
                                    accept="image/*"
                                    class="input"
                                    @change="handleImagePreview($event, vIndex)">
                                <div class="flex gap-2 mt-2 flex-wrap" x-ref="'preview' + vIndex"></div>
                            </div>

                            {{-- Attributes --}}
                            <div class="md:col-span-2 border-t pt-4 mt-2">
                                <div class="flex justify-between items-center mb-3">
                                    <label class="label mb-0">{{ __('Attributes') }}</label>
                                </div>
                                
                                {{-- Catalog Selector --}}
                                <div class="mb-3 p-3 bg-bg-soft rounded border border-brand-border">
                                    <label class="text-sm font-medium text-text mb-2 block">📚 Выбрать из справочника</label>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                        @foreach($attributeCatalog as $catalog)
                                            <button 
                                                type="button"
                                                @click="addAttributeFromCatalog(vIndex, @js($catalog))"
                                                class="text-left px-3 py-2 rounded bg-white hover:bg-brand hover:text-white transition border border-brand-border text-sm">
                                                <span class="font-medium">{{ $catalog->name }}</span>
                                                @if($catalog->type === 'select')
                                                    <span class="text-xs opacity-70 ml-1">({{ count($catalog->options) }} вариантов)</span>
                                                @endif
                                            </button>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Custom Attributes --}}
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm text-text-muted">Или добавьте свою характеристику:</span>
                                    <button 
                                        type="button"
                                        @click="addAttribute(vIndex)"
                                        class="text-brand text-sm hover:text-brand-hover">
                                        + {{ __('Add Attribute') }}
                                    </button>
                                </div>
                                
                                <div class="space-y-2">
                                    <template x-for="(attr, aIndex) in variant.attributes" :key="aIndex">
                                        <div class="space-y-1">
                                            <div class="flex gap-2 items-start">
                                                <div class="flex-1">
                                                    <input 
                                                        type="text" 
                                                        :name="'variants[' + vIndex + '][attributes][' + aIndex + '][name]'"
                                                        x-model="attr.name"
                                                        placeholder="{{ __('Name (e.g. Color)') }}"
                                                        :readonly="attr.catalogType"
                                                        :class="attr.catalogType ? 'bg-bg-soft' : ''"
                                                        class="input input-sm">
                                                </div>
                                                <div class="flex-1">
                                                    {{-- Select dropdown --}}
                                                    <template x-if="attr.catalogType === 'select' && attr.options && !attr.useCustomValue">
                                                        <select 
                                                            :name="'variants[' + vIndex + '][attributes][' + aIndex + '][value]'"
                                                            x-model="attr.value"
                                                            class="input input-sm">
                                                            <option value="">Выберите...</option>
                                                            <template x-for="option in attr.options" :key="option">
                                                                <option :value="option" x-text="option"></option>
                                                            </template>
                                                        </select>
                                                    </template>
                                                    {{-- Text input --}}
                                                    <template x-if="!attr.catalogType || attr.catalogType !== 'select' || attr.useCustomValue">
                                                        <input 
                                                            type="text" 
                                                            :name="'variants[' + vIndex + '][attributes][' + aIndex + '][value]'"
                                                            x-model="attr.value"
                                                            placeholder="{{ __('Value (e.g. Red)') }}"
                                                            class="input input-sm">
                                                    </template>
                                                </div>
                                                <button 
                                                    type="button"
                                                    @click="removeAttribute(vIndex, aIndex)"
                                                    class="btn btn-sm btn-error">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                            {{-- Toggle for custom value --}}
                                            <template x-if="attr.catalogType === 'select' && attr.options">
                                                <div class="flex justify-end">
                                                    <button 
                                                        type="button"
                                                        @click="attr.useCustomValue = !attr.useCustomValue; attr.value = ''"
                                                        class="text-xs text-brand hover:text-brand-hover">
                                                        <template x-if="!attr.useCustomValue">
                                                            <span>✏️ Ввести свое значение</span>
                                                        </template>
                                                        <template x-if="attr.useCustomValue">
                                                            <span>↩️ Выбрать из списка</span>
                                                        </template>
                                                    </button>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            {{-- Marketplace Links --}}
                            <div class="md:col-span-2 border-t pt-4 mt-2">
                                <div class="flex justify-between items-center mb-2">
                                    <label class="label mb-0">{{ __('Marketplace Links') }}</label>
                                    <button 
                                        type="button"
                                        @click="addMarketplaceLink(vIndex)"
                                        class="text-brand text-sm hover:text-brand-hover">
                                        + {{ __('Add Marketplace') }}
                                    </button>
                                </div>
                                <div class="space-y-3">
                                    <template x-for="(link, mIndex) in variant.marketplace_links" :key="mIndex">
                                        <div class="p-3 bg-bg-soft rounded border border-brand-border">
                                            <div class="grid grid-cols-2 gap-2">
                                                <div>
                                                    <label class="text-xs text-text-muted">{{ __('Marketplace') }}</label>
                                                    <select 
                                                        :name="'variants[' + vIndex + '][marketplace_links][' + mIndex + '][marketplace]'"
                                                        x-model="link.marketplace"
                                                        class="input input-sm">
                                                        <option value="">{{ __('Select...') }}</option>
                                                        @foreach($marketplaces as $mp)
                                                            <option value="{{ $mp }}">{{ ucfirst($mp) }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="text-xs text-text-muted">{{ __('Marketplace SKU') }}</label>
                                                    <input 
                                                        type="text" 
                                                        :name="'variants[' + vIndex + '][marketplace_links][' + mIndex + '][marketplace_sku]'"
                                                        x-model="link.marketplace_sku"
                                                        class="input input-sm">
                                                </div>
                                                <div>
                                                    <label class="text-xs text-text-muted">{{ __('Marketplace Barcode') }}</label>
                                                    <input 
                                                        type="text" 
                                                        :name="'variants[' + vIndex + '][marketplace_links][' + mIndex + '][marketplace_barcode]'"
                                                        x-model="link.marketplace_barcode"
                                                        class="input input-sm">
                                                </div>
                                                <div class="flex items-end justify-between">
                                                    <label class="flex items-center gap-2 cursor-pointer">
                                                        <input 
                                                            type="checkbox" 
                                                            :name="'variants[' + vIndex + '][marketplace_links][' + mIndex + '][sync_stock]'"
                                                            x-model="link.sync_stock"
                                                            value="1"
                                                            class="checkbox checkbox-sm">
                                                        <span class="text-xs">{{ __('Sync Stock') }}</span>
                                                    </label>
                                                    <button 
                                                        type="button"
                                                        @click="removeMarketplaceLink(vIndex, mIndex)"
                                                        class="btn btn-sm btn-error">
                                                        {{ __('Remove') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('cabinet.products.index') }}" class="btn btn-ghost">
                {{ __('Cancel') }}
            </a>
            <button type="submit" class="btn btn-primary">
                {{ __('Create Product') }}
            </button>
        </div>
    </form>
</div>

<script>
function productForm() {
    return {
        variants: [
            {
                variant_name: '',
                sku_code: '',
                barcode: '',
                dims_l: '',
                dims_w: '',
                dims_h: '',
                weight: '',
                price: '',
                cost_price: '',
                expenses: '',
                attributes: [],
                marketplace_links: []
            }
        ],
        
        addVariant() {
            this.variants.push({
                variant_name: '',
                sku_code: '',
                barcode: '',
                dims_l: '',
                dims_w: '',
                dims_h: '',
                weight: '',
                price: '',
                cost_price: '',
                expenses: '',
                attributes: [],
                marketplace_links: []
            });
        },
        
        removeVariant(index) {
            if (this.variants.length > 1) {
                this.variants.splice(index, 1);
            }
        },
        
        addAttribute(variantIndex) {
            this.variants[variantIndex].attributes.push({
                name: '',
                value: '',
                catalogType: null,
                options: null,
                useCustomValue: false
            });
        },
        
        addAttributeFromCatalog(variantIndex, catalog) {
            this.variants[variantIndex].attributes.push({
                name: catalog.name,
                value: '',
                catalogType: catalog.type,
                options: catalog.options,
                useCustomValue: false
            });
        },
        
        removeAttribute(variantIndex, attrIndex) {
            this.variants[variantIndex].attributes.splice(attrIndex, 1);
        },
        
        addMarketplaceLink(variantIndex) {
            this.variants[variantIndex].marketplace_links.push({
                marketplace: '',
                marketplace_sku: '',
                marketplace_barcode: '',
                sync_stock: false
            });
        },
        
        removeMarketplaceLink(variantIndex, linkIndex) {
            this.variants[variantIndex].marketplace_links.splice(linkIndex, 1);
        },
        
        handleImagePreview(event, variantIndex) {
            const files = event.target.files;
            const previewContainer = this.$refs['preview' + variantIndex];
            
            if (previewContainer) {
                previewContainer.innerHTML = '';
                
                Array.from(files).slice(0, 10).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'w-16 h-16 object-cover rounded border border-brand-border';
                        previewContainer.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                });
            }
        }
    }
}
</script>
@endsection
