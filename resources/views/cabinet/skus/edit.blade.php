@extends('cabinet.layout')

@section('title', __('Edit Product'))

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-h1 font-heading">{{ __('Edit Product') }}</h1>
            <p class="text-body-m text-text-muted mt-2">{{ $sku->title }}</p>
        </div>
        <a href="{{ route('cabinet.skus.index') }}" class="btn btn-secondary">
            ← {{ __('Back') }}
        </a>
    </div>
</div>

<div class="max-w-2xl">
    <div class="card">
        <form action="{{ route('cabinet.skus.update', $sku) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- SKU Code -->
                <div>
                    <label for="sku_code" class="block text-body-m font-semibold mb-2">{{ __('SKU Code') }} <span class="text-error">*</span></label>
                    <input type="text" name="sku_code" id="sku_code" value="{{ old('sku_code', $sku->sku_code) }}" required
                        class="input w-full @error('sku_code') border-error @enderror">
                    @error('sku_code')
                        <p class="text-error text-body-s mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Barcode -->
                <div>
                    <label for="barcode" class="block text-body-m font-semibold mb-2">{{ __('Barcode') }}</label>
                    <input type="text" name="barcode" id="barcode" value="{{ old('barcode', $sku->barcode) }}"
                        class="input w-full @error('barcode') border-error @enderror">
                    @error('barcode')
                        <p class="text-error text-body-s mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Title -->
            <div class="mt-6">
                <label for="title" class="block text-body-m font-semibold mb-2">{{ __('Product Title') }} <span class="text-error">*</span></label>
                <input type="text" name="title" id="title" value="{{ old('title', $sku->title) }}" required
                    class="input w-full @error('title') border-error @enderror">
                @error('title')
                    <p class="text-error text-body-s mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Dimensions -->
            <div class="mt-6">
                <label class="block text-body-m font-semibold mb-2">{{ __('Dimensions (cm)') }}</label>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <input type="number" step="0.01" name="dims_l" value="{{ old('dims_l', $sku->dims_l) }}"
                            class="input w-full @error('dims_l') border-error @enderror"
                            placeholder="{{ __('Length') }}">
                    </div>
                    <div>
                        <input type="number" step="0.01" name="dims_w" value="{{ old('dims_w', $sku->dims_w) }}"
                            class="input w-full @error('dims_w') border-error @enderror"
                            placeholder="{{ __('Width') }}">
                    </div>
                    <div>
                        <input type="number" step="0.01" name="dims_h" value="{{ old('dims_h', $sku->dims_h) }}"
                            class="input w-full @error('dims_h') border-error @enderror"
                            placeholder="{{ __('Height') }}">
                    </div>
                </div>
            </div>

            <!-- Weight -->
            <div class="mt-6">
                <label for="weight" class="block text-body-m font-semibold mb-2">{{ __('Weight (kg)') }}</label>
                <input type="number" step="0.001" name="weight" id="weight" value="{{ old('weight', $sku->weight) }}"
                    class="input w-48 @error('weight') border-error @enderror">
                @error('weight')
                    <p class="text-error text-body-s mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Current Photo -->
            @if($sku->photo_path)
            <div class="mt-6">
                <label class="block text-body-m font-semibold mb-2">{{ __('Current Photo') }}</label>
                <img src="{{ asset('storage/' . $sku->photo_path) }}" alt="{{ $sku->title }}" 
                     class="w-32 h-32 rounded-btn object-cover">
            </div>
            @endif

            <!-- Photo -->
            <div class="mt-6">
                <label for="photo" class="block text-body-m font-semibold mb-2">{{ $sku->photo_path ? __('Replace Photo') : __('Product Photo') }}</label>
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
                    <input type="checkbox" name="is_active" value="1" {{ $sku->is_active ? 'checked' : '' }}
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
                    {{ __('Save Changes') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
