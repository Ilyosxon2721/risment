@extends('cabinet.layout')

@section('title', __('Add Product'))

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-h1 font-heading">{{ __('Add Product') }}</h1>
            <p class="text-body-m text-text-muted mt-2">{{ __('Create a new product in your catalog') }}</p>
        </div>
        <a href="{{ route('cabinet.skus.index') }}" class="btn btn-secondary">
            ← {{ __('Back') }}
        </a>
    </div>
</div>

<div class="max-w-2xl">
    <div class="card">
        <form action="{{ route('cabinet.skus.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- SKU Code -->
                <div>
                    <label for="sku_code" class="block text-body-m font-semibold mb-2">{{ __('SKU Code') }} <span class="text-error">*</span></label>
                    <input type="text" name="sku_code" id="sku_code" value="{{ old('sku_code') }}" required
                        class="input w-full @error('sku_code') border-error @enderror"
                        placeholder="PROD-001">
                    @error('sku_code')
                        <p class="text-error text-body-s mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Barcode -->
                <div>
                    <label for="barcode" class="block text-body-m font-semibold mb-2">{{ __('Barcode') }}</label>
                    <input type="text" name="barcode" id="barcode" value="{{ old('barcode') }}"
                        class="input w-full @error('barcode') border-error @enderror"
                        placeholder="4607001234567">
                    @error('barcode')
                        <p class="text-error text-body-s mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Title -->
            <div class="mt-6">
                <label for="title" class="block text-body-m font-semibold mb-2">{{ __('Product Title') }} <span class="text-error">*</span></label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required
                    class="input w-full @error('title') border-error @enderror"
                    placeholder="{{ __('Enter product name') }}">
                @error('title')
                    <p class="text-error text-body-s mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Dimensions -->
            <div class="mt-6">
                <label class="block text-body-m font-semibold mb-2">{{ __('Dimensions (cm)') }}</label>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <input type="number" step="0.01" name="dims_l" value="{{ old('dims_l') }}"
                            class="input w-full @error('dims_l') border-error @enderror"
                            placeholder="{{ __('Length') }}">
                    </div>
                    <div>
                        <input type="number" step="0.01" name="dims_w" value="{{ old('dims_w') }}"
                            class="input w-full @error('dims_w') border-error @enderror"
                            placeholder="{{ __('Width') }}">
                    </div>
                    <div>
                        <input type="number" step="0.01" name="dims_h" value="{{ old('dims_h') }}"
                            class="input w-full @error('dims_h') border-error @enderror"
                            placeholder="{{ __('Height') }}">
                    </div>
                </div>
            </div>

            <!-- Weight -->
            <div class="mt-6">
                <label for="weight" class="block text-body-m font-semibold mb-2">{{ __('Weight (kg)') }}</label>
                <input type="number" step="0.001" name="weight" id="weight" value="{{ old('weight') }}"
                    class="input w-48 @error('weight') border-error @enderror"
                    placeholder="0.500">
                @error('weight')
                    <p class="text-error text-body-s mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Photo -->
            <div class="mt-6">
                <label for="photo" class="block text-body-m font-semibold mb-2">{{ __('Product Photo') }}</label>
                <input type="file" name="photo" id="photo" accept="image/*"
                    class="block w-full text-body-m text-text-muted
                           file:mr-4 file:py-2 file:px-4
                           file:rounded-btn file:border-0
                           file:bg-brand file:text-white
                           hover:file:bg-brand-dark
                           cursor-pointer">
                <p class="text-body-s text-text-muted mt-1">{{ __('Max 5MB. JPG, PNG, GIF.') }}</p>
                @error('photo')
                    <p class="text-error text-body-s mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div class="mt-6">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked
                        class="w-5 h-5 rounded border-brand-border text-brand focus:ring-brand">
                    <span class="text-body-m font-semibold">{{ __('Active') }}</span>
                </label>
                <p class="text-body-s text-text-muted mt-1">{{ __('Inactive products will not appear in inbounds and shipments.') }}</p>
            </div>

            <!-- Submit -->
            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('cabinet.skus.index') }}" class="btn btn-ghost">
                    {{ __('Cancel') }}
                </a>
                <button type="submit" class="btn btn-primary">
                    {{ __('Create Product') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
