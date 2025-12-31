@extends('cabinet.layout')

@section('title', __('Shipments'))

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-h1 font-heading">{{ __('Shipments') }}</h1>
            <p class="text-body-m text-text-muted mt-2">{{ __('Manage FBO shipments to marketplaces') }}</p>
        </div>
        <a href="{{ route('cabinet.shipments.create') }}" class="btn btn-primary">
            + {{ __('Create Shipment') }}
        </a>
    </div>
</div>

@if($shipments->isEmpty())
<div class="card text-center py-12">
    <svg class="w-16 h-16 text-text-muted mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
    </svg>
    <h3 class="text-h3 font-heading text-text-muted mb-2">{{ __('No shipments yet') }}</h3>
    <p class="text-body-m text-text-muted mb-6">{{ __('Create your first shipment to send products to marketplace warehouses') }}</p>
    <a href="{{ route('cabinet.shipments.create') }}" class="btn btn-primary">
        + {{ __('Create Shipment') }}
    </a>
</div>
@else
<div class="card">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-bg-soft">
                <tr>
                    <th class="px-4 py-3 text-left text-body-s font-semibold">ID</th>
                    <th class="px-4 py-3 text-left text-body-s font-semibold">{{ __('Marketplace') }}</th>
                    <th class="px-4 py-3 text-left text-body-s font-semibold">{{ __('Warehouse') }}</th>
                    <th class="px-4 py-3 text-left text-body-s font-semibold">{{ __('Status') }}</th>
                    <th class="px-4 py-3 text-right text-body-s font-semibold">{{ __('Items') }}</th>
                    <th class="px-4 py-3 text-left text-body-s font-semibold">{{ __('Planned Date') }}</th>
                    <th class="px-4 py-3 text-left text-body-s font-semibold">{{ __('Created') }}</th>
                    <th class="px-4 py-3 text-right text-body-s font-semibold">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($shipments as $shipment)
                <tr class="border-t border-brand-border hover:bg-bg-soft transition">
                    <td class="px-4 py-3 font-mono text-body-s">#{{ $shipment->id }}</td>
                    <td class="px-4 py-3">
                        <span class="font-semibold">{{ __(ucfirst($shipment->marketplace)) }}</span>
                    </td>
                    <td class="px-4 py-3">{{ $shipment->warehouse_name }}</td>
                    <td class="px-4 py-3">
                        <span class="badge badge-{{ 
                            $shipment->status === 'draft' ? 'warning' : 
                            ($shipment->status === 'shipped' ? 'success' : 'info') 
                        }}">
                            {{ __(ucfirst($shipment->status)) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">{{ $shipment->items->count() }}</td>
                    <td class="px-4 py-3">
                        {{ $shipment->planned_at ? \Carbon\Carbon::parse($shipment->planned_at)->format('d.m.Y') : '-' }}
                    </td>
                    <td class="px-4 py-3">{{ $shipment->created_at->format('d.m.Y') }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('cabinet.shipments.show', ['shipment' => $shipment]) }}" 
                           class="text-brand hover:underline text-body-s">
                            {{ __('View') }}
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    @if($shipments->hasPages())
    <div class="px-4 py-4 border-t border-brand-border">
        {{ $shipments->links() }}
    </div>
    @endif
</div>
@endif
@endsection
