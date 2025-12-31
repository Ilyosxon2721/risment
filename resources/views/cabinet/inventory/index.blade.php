@extends('cabinet.layout')

@section('title', __('Inventory'))

@section('content')
<div class="flex justify-between items-center mb-8">
    <h1 class="text-h1 font-heading">{{ __('Inventory') }}</h1>
</div>

<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="card">
        <div class="text-body-s text-text-muted mb-2">{{ __('Total SKUs') }}</div>
        <div class="text-h2 font-heading text-brand">{{ number_format($stats['total_skus']) }}</div>
    </div>
    <div class="card">
        <div class="text-body-s text-text-muted mb-2">{{ __('Total Quantity') }}</div>
        <div class="text-h2 font-heading text-brand">{{ number_format($stats['total_qty']) }}</div>
    </div>
    <div class="card">
        <div class="text-body-s text-text-muted mb-2">{{ __('Reserved') }}</div>
        <div class="text-h2 font-heading text-warning">{{ number_format($stats['total_reserved']) }}</div>
    </div>
    <div class="card">
        <div class="text-body-s text-text-muted mb-2">{{ __('Available') }}</div>
        <div class="text-h2 font-heading text-success">{{ number_format($stats['available_qty']) }}</div>
    </div>
</div>

<!-- Search -->
<div class="card mb-6">
    <form method="GET" class="flex gap-4">
        <input type="text" name="search" placeholder="{{ __('Search by SKU, title, or barcode') }}" 
               value="{{ request('search') }}" 
               class="input flex-1">
        <button type="submit" class="btn btn-primary">{{ __('Search') }}</button>
    </form>
</div>

<!-- Inventory Table -->
<div class="card">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-brand-border">
                    <th class="text-left p-4">{{ __('SKU Code') }}</th>
                    <th class="text-left p-4">{{ __('Title') }}</th>
                    <th class="text-left p-4">{{ __('Barcode') }}</th>
                    <th class="text-right p-4">{{ __('Total') }}</th>
                    <th class="text-right p-4">{{ __('Reserved') }}</th>
                    <th class="text-right p-4">{{ __('Available') }}</th>
                    <th class="text-left p-4">{{ __('Location') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inventory as $item)
                <tr class="border-b border-brand-border hover:bg-bg-soft">
                    <td class="p-4 font-semibold">{{ $item->sku->sku_code }}</td>
                    <td class="p-4">{{ $item->sku->title }}</td>
                    <td class="p-4">{{ $item->sku->barcode ?? '-' }}</td>
                    <td class="p-4 text-right">{{ number_format($item->qty_total) }}</td>
                    <td class="p-4 text-right text-warning">{{ number_format($item->qty_reserved) }}</td>
                    <td class="p-4 text-right text-success font-semibold">{{ number_format($item->available_qty) }}</td>
                    <td class="p-4">{{ $item->location_code ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="p-8 text-center text-text-muted">{{ __('No inventory found') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="p-4 border-t border-brand-border">
        {{ $inventory->links() }}
    </div>
</div>
@endsection
