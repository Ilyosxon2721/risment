@extends('manager.layout')

@section('title', __('Shipments'))

@section('content')
<div class="flex justify-between items-center mb-8">
    <h2 class="text-h2 font-heading">{{ __('Client shipments') }}</h2>
</div>

<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-card border border-brand-border p-4">
        <div class="text-body-s text-text-muted">{{ __('Total') }}</div>
        <div class="text-h3 font-heading mt-1">{{ number_format($stats['total']) }}</div>
    </div>
    <div class="bg-white rounded-card border border-brand-border p-4">
        <div class="text-body-s text-text-muted">{{ __('Pending') }}</div>
        <div class="text-h3 font-heading mt-1 text-warning">{{ number_format($stats['pending']) }}</div>
    </div>
    <div class="bg-white rounded-card border border-brand-border p-4">
        <div class="text-body-s text-text-muted">{{ __('In progress') }}</div>
        <div class="text-h3 font-heading mt-1 text-brand">{{ number_format($stats['in_progress']) }}</div>
    </div>
    <div class="bg-white rounded-card border border-brand-border p-4">
        <div class="text-body-s text-text-muted">{{ __('Shipped') }}</div>
        <div class="text-h3 font-heading mt-1 text-success">{{ number_format($stats['shipped']) }}</div>
    </div>
</div>

<!-- Filters -->
<div class="flex gap-4 mb-6 flex-wrap">
    <form method="GET" class="flex gap-4 flex-wrap">
        <select name="status" onchange="this.form.submit()" class="input">
            <option value="">{{ __('All statuses') }}</option>
            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>{{ __('Draft') }}</option>
            <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>{{ __('Submitted') }}</option>
            <option value="picking" {{ request('status') === 'picking' ? 'selected' : '' }}>{{ __('Picking') }}</option>
            <option value="packed" {{ request('status') === 'packed' ? 'selected' : '' }}>{{ __('Packed') }}</option>
            <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>{{ __('Shipped') }}</option>
            <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>{{ __('Delivered') }}</option>
            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
        </select>
        <select name="marketplace" onchange="this.form.submit()" class="input">
            <option value="">{{ __('All marketplaces') }}</option>
            <option value="uzum" {{ request('marketplace') === 'uzum' ? 'selected' : '' }}>Uzum</option>
            <option value="wb" {{ request('marketplace') === 'wb' ? 'selected' : '' }}>Wildberries</option>
            <option value="ozon" {{ request('marketplace') === 'ozon' ? 'selected' : '' }}>Ozon</option>
            <option value="yandex" {{ request('marketplace') === 'yandex' ? 'selected' : '' }}>Yandex</option>
        </select>
    </form>
</div>

<!-- Shipments Table -->
<div class="bg-white rounded-card border border-brand-border">
    <div class="table-responsive relative">
        <table class="w-full responsive-table">
            <thead class="bg-bg-soft">
                <tr>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted">#</th>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted">{{ __('Date') }}</th>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted">{{ __('Marketplace') }}</th>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted">{{ __('Warehouse') }}</th>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted">{{ __('Products') }}</th>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted">{{ __('Status') }}</th>
                    <th class="px-6 py-3 text-left text-body-s font-semibold text-text-muted"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-brand-border">
                @forelse($shipments as $shipment)
                @php
                    $statusColors = [
                        'draft' => 'gray',
                        'submitted' => 'yellow',
                        'picking' => 'blue',
                        'packed' => 'purple',
                        'shipped' => 'green',
                        'delivered' => 'green',
                        'cancelled' => 'red',
                    ];
                    $statusLabels = [
                        'draft' => __('Draft'),
                        'submitted' => __('Submitted'),
                        'picking' => __('Picking'),
                        'packed' => __('Packed'),
                        'shipped' => __('Shipped'),
                        'delivered' => __('Delivered'),
                        'cancelled' => __('Cancelled'),
                    ];
                @endphp
                <tr class="hover:bg-bg-soft">
                    <td class="px-6 py-4 text-body-s font-mono" data-label="#">#{{ $shipment->id }}</td>
                    <td class="px-6 py-4 text-body-s" data-label="{{ __('Date') }}">{{ $shipment->created_at->format('d.m.Y H:i') }}</td>
                    <td class="px-6 py-4" data-label="{{ __('Marketplace') }}">
                        <span class="uppercase text-body-s font-semibold">{{ $shipment->marketplace }}</span>
                    </td>
                    <td class="px-6 py-4 text-body-s" data-label="{{ __('Warehouse') }}">{{ $shipment->warehouse_name ?? '-' }}</td>
                    <td class="px-6 py-4 text-body-s" data-label="{{ __('Products') }}">{{ $shipment->items->sum('qty') }}</td>
                    <td class="px-6 py-4" data-label="{{ __('Status') }}">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-{{ $statusColors[$shipment->status] ?? 'gray' }}-100 text-{{ $statusColors[$shipment->status] ?? 'gray' }}-800">
                            {{ $statusLabels[$shipment->status] ?? $shipment->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('manager.shipments.show', $shipment) }}" class="text-brand hover:underline text-body-s">{{ __('Details') }}</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-text-muted">{{ __('No shipments') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($shipments->hasPages())
    <div class="p-4 border-t border-brand-border">
        {{ $shipments->links() }}
    </div>
    @endif
</div>
@endsection