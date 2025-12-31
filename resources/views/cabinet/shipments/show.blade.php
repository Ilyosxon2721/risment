@extends('cabinet.layout')

@section('title', __('Shipment') . ' #' . $shipment->id)

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-h1 font-heading">{{ __('Shipment') }} #{{ $shipment->id }}</h1>
            <p class="text-body-m text-text-muted mt-2">
                {{ __(ucfirst($shipment->marketplace)) }} → {{ $shipment->warehouse_name }}
            </p>
        </div>
        <a href="{{ route('cabinet.shipments.index') }}" class="btn btn-ghost">
            ← {{ __('Back') }}
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Main Content -->
    <div class="lg:col-span-2">
        <!-- Information Card -->
        <div class="card mb-6">
            <h2 class="text-h3 font-heading mb-6">{{ __('Shipment Information') }}</h2>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <div class="text-body-s text-text-muted mb-1">{{ __('Marketplace') }}</div>
                    <div class="font-semibold">{{ __(ucfirst($shipment->marketplace)) }}</div>
                </div>
                
                <div>
                    <div class="text-body-s text-text-muted mb-1">{{ __('Status') }}</div>
                    <div>
                        <span class="badge badge-{{ 
                            $shipment->status === 'draft' ? 'warning' : 
                            ($shipment->status === 'shipped' ? 'success' : 'info') 
                        }}">
                            {{ __(ucfirst($shipment->status)) }}
                        </span>
                    </div>
                </div>
                
                <div>
                    <div class="text-body-s text-text-muted mb-1">{{ __('Warehouse') }}</div>
                    <div class="font-semibold">{{ $shipment->warehouse_name }}</div>
                </div>
                
                @if($shipment->planned_at)
                <div>
                    <div class="text-body-s text-text-muted mb-1">{{ __('Planned Date') }}</div>
                    <div class="font-semibold">{{ \Carbon\Carbon::parse($shipment->planned_at)->format('d.m.Y') }}</div>
                </div>
                @endif
                
                <div>
                    <div class="text-body-s text-text-muted mb-1">{{ __('Created') }}</div>
                    <div class="font-semibold">{{ $shipment->created_at->format('d.m.Y H:i') }}</div>
                </div>
                
                @if($shipment->shipped_at)
                <div>
                    <div class="text-body-s text-text-muted mb-1">{{ __('Shipped Date') }}</div>
                    <div class="font-semibold">{{ \Carbon\Carbon::parse($shipment->shipped_at)->format('d.m.Y H:i') }}</div>
                </div>
                @endif
            </div>
            
            @if($shipment->notes)
            <div class="mt-6 pt-6 border-t border-brand-border">
                <div class="text-body-s text-text-muted mb-2">{{ __('Notes') }}</div>
                <div class="text-body-m">{{ $shipment->notes }}</div>
            </div>
            @endif
        </div>
        
        <!-- Items -->
        <div class="card">
            <h2 class="text-h3 font-heading mb-6">{{ __('Items') }}</h2>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-bg-soft">
                        <tr>
                            <th class="px-4 py-3 text-left text-body-s font-semibold">{{ __('SKU') }}</th>
                            <th class="px-4 py-3 text-left text-body-s font-semibold">{{ __('Name') }}</th>
                            <th class="px-4 py-3 text-right text-body-s font-semibold">{{ __('Quantity') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($shipment->items as $item)
                        <tr class="border-t border-brand-border">
                            <td class="px-4 py-3 font-mono text-body-s">{{ $item->sku->sku }}</td>
                            <td class="px-4 py-3">{{ $item->sku->name }}</td>
                            <td class="px-4 py-3 text-right font-semibold">{{ number_format($item->qty) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-bg-soft border-t-2 border-brand-dark">
                        <tr>
                            <td colspan="2" class="px-4 py-3 font-semibold">{{ __('Total') }}</td>
                            <td class="px-4 py-3 text-right font-semibold">
                                {{ number_format($shipment->items->sum('qty')) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Sidebar -->
    <div class="lg:col-span-1">
        <div class="card sticky top-4">
            <h3 class="text-h4 font-heading mb-4">{{ __('Summary') }}</h3>
            
            <div class="space-y-3 text-body-s">
                <div class="flex justify-between">
                    <span class="text-text-muted">{{ __('Total Items') }}</span>
                    <span class="font-semibold">{{ $shipment->items->count() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-text-muted">{{ __('Total Units') }}</span>
                    <span class="font-semibold">{{ number_format($shipment->items->sum('qty')) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
