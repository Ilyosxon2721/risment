@extends('cabinet.layout')

@section('title', __('Products'))

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-h1 font-heading">{{ __('Products') }}</h1>
            <p class="text-body-m text-text-muted mt-2">{{ __('Manage your product catalog') }}</p>
        </div>
        <a href="{{ route('cabinet.products.create') }}" class="btn btn-primary">
            + {{ __('Add Product') }}
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card mb-6">
    <form action="{{ route('cabinet.skus.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-body-s font-semibold mb-2">{{ __('Search') }}</label>
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="{{ __('SKU, barcode, or title...') }}"
                   class="input w-full">
        </div>
        <div class="w-40">
            <label class="block text-body-s font-semibold mb-2">{{ __('Status') }}</label>
            <select name="status" class="input w-full">
                <option value="">{{ __('All') }}</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
            </select>
        </div>
        <div>
            <button type="submit" class="btn btn-secondary">{{ __('Filter') }}</button>
        </div>
        @if(request()->hasAny(['search', 'status']))
        <div>
            <a href="{{ route('cabinet.skus.index') }}" class="btn btn-ghost">{{ __('Clear') }}</a>
        </div>
        @endif
    </form>
</div>

<!-- Products Table -->
<div class="card">
    @if($skus->count() > 0)
    <div class="overflow-x-auto">
        <table class="table w-full">
            <thead>
                <tr>
                    <th class="text-left">{{ __('Product') }}</th>
                    <th>{{ __('SKU Code') }}</th>
                    <th>{{ __('Barcode') }}</th>
                    <th>{{ __('Dimensions') }}</th>
                    <th>{{ __('Weight') }}</th>
                    <th>{{ __('Stock') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($skus as $sku)
                <tr>
                    <td>
                        <div class="flex items-center gap-3">
                            @if($sku->photo_path)
                            <img src="{{ asset('storage/' . $sku->photo_path) }}" 
                                 alt="{{ $sku->title }}" 
                                 class="w-12 h-12 rounded-btn object-cover">
                            @else
                            <div class="w-12 h-12 rounded-btn bg-bg-soft flex items-center justify-center">
                                <svg class="w-6 h-6 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            @endif
                            <div>
                                <div class="font-semibold">{{ $sku->title }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="text-center font-mono">{{ $sku->sku_code }}</td>
                    <td class="text-center font-mono">{{ $sku->barcode ?? '—' }}</td>
                    <td class="text-center text-body-s">
                        @if($sku->dims_l && $sku->dims_w && $sku->dims_h)
                        {{ $sku->dims_l }}×{{ $sku->dims_w }}×{{ $sku->dims_h }} см
                        @else
                        —
                        @endif
                    </td>
                    <td class="text-center">{{ $sku->weight ? $sku->weight . ' кг' : '—' }}</td>
                    <td class="text-center font-semibold">
                        {{ $sku->inventory ? $sku->inventory->qty_total : 0 }}
                    </td>
                    <td class="text-center">
                        <span class="badge badge-{{ $sku->is_active ? 'success' : 'secondary' }}">
                            {{ $sku->is_active ? __('Active') : __('Inactive') }}
                        </span>
                    </td>
                    <td class="text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('cabinet.skus.edit', $sku) }}" class="btn btn-ghost btn-sm">
                                {{ __('Edit') }}
                            </a>
                            <form action="{{ route('cabinet.skus.destroy', $sku) }}" method="POST" 
                                  onsubmit="return confirm('{{ __('Are you sure you want to delete this product?') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-ghost btn-sm text-error">
                                    {{ __('Delete') }}
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="mt-6">
        {{ $skus->links() }}
    </div>
    @else
    <div class="text-center py-12">
        <svg class="w-16 h-16 mx-auto text-text-muted mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
        </svg>
        <h3 class="text-h4 font-heading mb-2">{{ __('No products yet') }}</h3>
        <p class="text-text-muted mb-6">{{ __('Start by adding your first product to the catalog.') }}</p>
        <a href="{{ route('cabinet.products.create') }}" class="btn btn-primary">
            + {{ __('Add Product') }}
        </a>
    </div>
    @endif
</div>
@endsection
