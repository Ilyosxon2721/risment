@extends('cabinet.layout')

@section('title', __('Edit Product'))

@section('content')
<div class="p-6" x-data="editProductForm(@json($product))">
    {{-- Header --}}
    <div class="mb-6">
        <a href="{{ route('cabinet.products.index') }}" class="text-brand hover:text-brand-hover inline-flex items-center mb-4">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            {{ __('Back to products') }}
        </a>
        <h1 class="text-h2 font-heading">{{ __('Edit Product') }}</h1>
    </div>

    <form method="POST" action="{{ route('cabinet.products.update', $product) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Product Info Card --}}
        <div class="card mb-6">
            <h2 class="text-h3 font-heading mb-4">{{ __('Product Information') }}</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="label" for="title">
                        {{ __('Product Title') }} <span class="text-error">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="title" 
                        id="title"
                        value="{{ old('title', $product->title) }}"
                        class="input @error('title') border-error @enderror"
                        required>
                    @error('title')
                        <p class="text-error text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="label" for="article">
                        {{ __('Article') }} <span class="text-error">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="article" 
                        id="article"
                        value="{{ old('article', $product->article) }}"
                        class="input @error('article') border-error @enderror"
                        required>
                    @error('article')
                        <p class="text-error text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="label">{{ __('Status') }}</label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input 
                            type="checkbox" 
                            name="is_active" 
                            value="1"
                            {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                            class="checkbox">
                        <span>{{ __('Active') }}</span>
                    </label>
                </div>

                <div class="md:col-span-2">
                    <label class="label" for="description">{{ __('Description') }}</label>
                    <textarea 
                        name="description" 
                        id="description"
                        rows="3"
                        class="input">{{ old('description', $product->description) }}</textarea>
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
                        <input type="hidden" :name="'variants[' + vIndex + '][id]'" x-model="variant.id">
                        
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
                            {{-- Basic Fields - same as create --}}
                            <div class="md:col-span-2">
                                <label class="label">
                                    {{ __('Variant Name') }} <span class="text-error">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    :name="'variants[' + vIndex + '][variant_name]'"
                                    x-model="variant.variant_name"
                                    class="input"
                                    required>
                            </div>

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

                            <div>
                                <label class="label">{{ __('Barcode') }}</label>
                                <input 
                                    type="text" 
                                    :name="'variants[' + vIndex + '][barcode]'"
                                    x-model="variant.barcode"
                                    class="input">
                            </div>

                            <div class="md:col-span-2">
                                <label class="label">{{ __('Dimensions (L × W × H, cm)') }}</label>
                                <div class="grid grid-cols-3 gap-2">
                                    <input 
                                        type="number" 
                                        :name="'variants[' + vIndex + '][dims_l]'"
                                        x-model="variant.dims_l"
                                        step="0.1"
                                        class="input">
                                    <input 
                                        type="number" 
                                        :name="'variants[' + vIndex + '][dims_w]'"
                                        x-model="variant.dims_w"
                                        step="0.1"
                                        class="input">
                                    <input 
                                        type="number" 
                                        :name="'variants[' + vIndex + '][dims_h]'"
                                        x-model="variant.dims_h"
                                        step="0.1"
                                        class="input">
                                </div>
                            </div>

                            <div>
                                <label class="label">{{ __('Weight (kg)') }}</label>
                                <input 
                                    type="number" 
                                    :name="'variants[' + vIndex + '][weight]'"
                                    x-model="variant.weight"
                                    step="0.01"
                                    class="input">
                            </div>

                            <div>
                                <label class="label">{{ __('Price') }}</label>
                                <input 
                                    type="number" 
                                    :name="'variants[' + vIndex + '][price]'"
                                    x-model="variant.price"
                                    step="0.01"
                                    class="input">
                            </div>

                            {{-- Existing Images --}}
                            <div class="md:col-span-2" x-show="variant.existing_images && variant.existing_images.length > 0">
                                <label class="label">{{ __('Existing Images') }}</label>
                                <div class="flex gap-2 flex-wrap">
                                    <template x-for="(img, imgIndex) in variant.existing_images" :key="imgIndex">
                                        <div class="relative">
                                            <img :src="'/storage/' + img.image_path" class="w-20 h-20 object-cover rounded border">
                                            <span x-show="img.is_primary" class="absolute top-0 right-0 bg-brand text-white text-xs px-1 rounded">{{ __('Primary') }}</span>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            {{-- Add New Images --}}
                            <div class="md:col-span-2">
                                <label class="label">{{ __('Add New Images') }}</label>
                                <input 
                                    type="file" 
                                    :name="'variants[' + vIndex + '][new_images][]'"
                                    multiple
                                    accept="image/*"
                                    class="input"
                                    @change="handleImagePreview($event, vIndex)">
                                <div class="flex gap-2 mt-2 flex-wrap" :ref="'preview' + vIndex"></div>
                            </div>

                            {{-- Attributes --}}
                            <div class="md:col-span-2 border-t pt-4 mt-2">
                                <div class="flex justify-between items-center mb-2">
                                    <label class="label mb-0">{{ __('Attributes') }}</label>
                                    <button 
                                        type="button"
                                        @click="addAttribute(vIndex)"
                                        class="text-brand text-sm hover:text-brand-hover">
                                        + {{ __('Add Attribute') }}
                                    </button>
                                </div>
                                <div class="space-y-2">
                                    <template x-for="(attr, aIndex) in variant.attributes" :key="aIndex">
                                        <div class="flex gap-2">
                                            <input 
                                                type="text" 
                                                :name="'variants[' + vIndex + '][attributes][' + aIndex + '][name]'"
                                                x-model="attr.name"
                                                placeholder="{{ __('Name') }}"
                                                class="input flex-1">
                                            <input 
                                                type="text" 
                                                :name="'variants[' + vIndex + '][attributes][' + aIndex + '][value]'"
                                                x-model="attr.value"
                                                placeholder="{{ __('Value') }}"
                                                class="input flex-1">
                                            <button 
                                                type="button"
                                                @click="removeAttribute(vIndex, aIndex)"
                                                class="btn btn-sm btn-error">×</button>
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
                                        class="text-brand text-sm">+ {{ __('Add Marketplace') }}</button>
                                </div>
                                <div class="space-y-2">
                                    <template x-for="(link, mIndex) in variant.marketplace_links" :key="mIndex">
                                        <div class="p-2 bg-bg-soft rounded border">
                                            <div class="grid grid-cols-2 gap-2 text-sm">
                                                <select 
                                                    :name="'variants[' + vIndex + '][marketplace_links][' + mIndex + '][marketplace]'"
                                                    x-model="link.marketplace"
                                                    class="input input-sm">
                                                    <option value="">{{ __('Select...') }}</option>
                                                    @foreach($marketplaces as $mp)
                                                        <option value="{{ $mp }}">{{ ucfirst($mp) }}</option>
                                                    @endforeach
                                                </select>
                                                <input 
                                                    type="text" 
                                                    :name="'variants[' + vIndex + '][marketplace_links][' + mIndex + '][marketplace_sku]'"
                                                    x-model="link.marketplace_sku"
                                                    placeholder="{{ __('SKU') }}"
                                                    class="input input-sm">
                                                <input 
                                                    type="text" 
                                                    :name="'variants[' + vIndex + '][marketplace_links][' + mIndex + '][marketplace_barcode]'"
                                                    x-model="link.marketplace_barcode"
                                                    placeholder="{{ __('Barcode') }}"
                                                    class="input input-sm">
                                                <div class="flex items-center justify-between">
                                                    <label class="flex items-center gap-1 text-xs">
                                                        <input 
                                                            type="checkbox" 
                                                            :name="'variants[' + vIndex + '][marketplace_links][' + mIndex + '][sync_stock]'"
                                                            x-model="link.sync_stock"
                                                            value="1"
                                                            class="checkbox checkbox-sm">
                                                        {{ __('Sync') }}
                                                    </label>
                                                    <button 
                                                        type="button"
                                                        @click="removeMarketplaceLink(vIndex, mIndex)"
                                                        class="btn btn-sm btn-error">×</button>
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
                {{ __('Update Product') }}
            </button>
        </div>
    </form>
</div>

<script>
function editProductForm(product) {
    return {
        variants: product.variants.map(v => ({
            id: v.id,
            variant_name: v.variant_name,
            sku_code: v.sku_code,
            barcode: v.barcode || '',
            dims_l: v.dims_l || '',
            dims_w: v.dims_w || '',
            dims_h: v.dims_h || '',
            weight: v.weight || '',
            price: v.price || '',
            existing_images: v.images || [],
            attributes: v.attributes.map(a => ({
                name: a.attribute_name,
                value: a.attribute_value
            })),
            marketplace_links: v.marketplace_links.map(l => ({
                marketplace: l.marketplace,
                marketplace_sku: l.marketplace_sku,
                marketplace_barcode: l.marketplace_barcode || '',
                sync_stock: l.sync_stock
            }))
        })),
        
        addVariant() {
            this.variants.push({
                id: null,
                variant_name: '',
                sku_code: '',
                barcode: '',
                dims_l: '',
                dims_w: '',
                dims_h: '',
                weight: '',
                price: '',
                existing_images: [],
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
            this.variants[variantIndex].attributes.push({ name: '', value: '' });
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
                        img.className = 'w-16 h-16 object-cover rounded border';
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
