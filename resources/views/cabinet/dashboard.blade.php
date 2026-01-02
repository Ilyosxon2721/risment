@extends('cabinet.layout')

@section('title', __('Dashboard'))

@section('content')
<div class="mb-8">
    <h1 class="text-h1 font-heading">{{ __('Dashboard') }}</h1>
    <p class="text-body-m text-text-muted mt-2">{{ __('Welcome back') }}, {{ Auth::user()->name }}</p>
</div>

<!-- Subscription Plan Widget -->
@if($plan)
<div class="card mb-8 {{ $plan->code === 'pro' ? 'border-2 border-brand' : '' }}">
    <div class="flex justify-between items-start mb-6">
        <div>
            <h2 class="text-h3 font-heading">{{ __('Your package') }}: {{ $plan->getName() }}</h2>
            <p class="text-body-s text-text-muted mt-1">{{ $plan->getDescription() }}</p>
        </div>
        <div class="text-right">
            <div class="text-h3 text-brand">{{ number_format($plan->price_month, 0, '', ' ') }} {{ __('UZS') }}</div>
            <div class="text-body-s text-text-muted">{{ __('/month') }}</div>
        </div>
    </div>
    
    <!-- Usage Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="p-4 bg-bg-soft rounded-btn">
            <div class="text-body-s text-text-muted mb-1">{{ __('FBS shipments') }}</div>
            <div class="text-h4 font-heading {{ ($usage && $usage->fbs_shipments_count > $plan->fbs_shipments_included) ? 'text-warning' : 'text-brand' }}">
                {{ $usage ? $usage->fbs_shipments_count : 0 }}
            </div>
            <div class="text-body-s text-text-muted">{{ __('out of') }} {{ $plan->fbs_shipments_included }}</div>
        </div>
        
        <div class="p-4 bg-bg-soft rounded-btn">
            <div class="text-body-s text-text-muted mb-1">{{ __('Boxes') }}</div>
            <div class="text-h4 font-heading {{ ($usage && $usage->storage_boxes_peak > $plan->storage_included_boxes) ? 'text-warning' : 'text-brand' }}">
                {{ $usage ? $usage->storage_boxes_peak : 0 }}
            </div>
            <div class="text-body-s text-text-muted">{{ __('out of') }} {{ $plan->storage_included_boxes }}</div>
        </div>
        
        <div class="p-4 bg-bg-soft rounded-btn">
            <div class="text-body-s text-text-muted mb-1">{{ __('Bags') }}</div>
            <div class="text-h4 font-heading {{ ($usage && $usage->storage_bags_peak > $plan->storage_included_bags) ? 'text-warning' : 'text-brand' }}">
                {{ $usage ? $usage->storage_bags_peak : 0 }}
            </div>
            <div class="text-body-s text-text-muted">{{ __('out of') }} {{ $plan->storage_included_bags }}</div>
        </div>
        
        <div class="p-4 bg-bg-soft rounded-btn">
            <div class="text-body-s text-text-muted mb-1">{{ __('Inbound') }}</div>
            <div class="text-h4 font-heading {{ ($usage && $usage->inbound_boxes_count > $plan->inbound_included_boxes) ? 'text-warning' : 'text-brand' }}">
                {{ $usage ? $usage->inbound_boxes_count : 0 }}
            </div>
            <div class="text-body-s text-text-muted">{{ __('out of') }} {{ $plan->inbound_included_boxes }}</div>
        </div>
    </div>
    
    <!-- Overage Warning -->
    @if($overageEstimate && $overageEstimate['total'] > 0)
    <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-btn">
        <div class="flex justify-between items-center">
            <div>
                <div class="font-semibold text-yellow-800">{{ __('Overage this month') }}</div>
                <div class="text-body-s text-yellow-700 mt-1">{{ __('Additional charges will be applied') }}</div>
            </div>
            <div class="text-h3 text-warning">+ {{ number_format($overageEstimate['total'], 0, '', ' ') }} {{ __('UZS') }}</div>
        </div>
    </div>
    @endif
    
    <!-- Shipping Schedule -->
    <div class="mt-6 pt-6 border-t border-brand-border">
        <div class="flex items-center gap-2 text-body-s">
            <svg class="w-5 h-5 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span class="text-text-muted">{{ __('Shipping schedule:') }}</span>
            <span class="font-semibold">{{ __('3 times a week (Mon/Wed/Fri), cut-off 12:00') }}</span>
        </div>
    </div>
</div>
@else
<div class="card mb-8 bg-yellow-50 border border-yellow-200">
    <div class="flex items-start gap-4">
        <svg class="w-6 h-6 text-warning flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>
        <div>
            <h3 class="text-h4 font-heading text-yellow-800 mb-2">{{ __('No package selected') }}</h3>
            <p class="text-body-s text-yellow-700 mb-4">{{ __('Choose a package for optimal service') }}</p>
            <a href="{{ route('cabinet.subscription.choose') }}" class="btn btn-primary">{{ __('Choose package') }}</a>
        </div>
    </div>
</div>
@endif

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6 mb-8">
    <div class="card">
        <div class="text-body-s text-text-muted mb-2">{{ __('Total SKUs') }}</div>
        <div class="text-h2 font-heading text-brand">{{ number_format($stats['total_skus']) }}</div>
    </div>
    
    <div class="card">
        <div class="text-body-s text-text-muted mb-2">{{ __('Total Inventory') }}</div>
        <div class="text-h2 font-heading text-brand">{{ number_format($stats['total_inventory']) }}</div>
    </div>
    
    <div class="card">
        <div class="text-body-s text-text-muted mb-2">{{ __('Pending Inbounds') }}</div>
        <div class="text-h2 font-heading text-warning">{{ $stats['pending_inbounds'] }}</div>
    </div>
    
    <div class="card">
        <div class="text-body-s text-text-muted mb-2">{{ __('Active Shipments') }}</div>
        <div class="text-h2 font-heading text-info">{{ $stats['active_shipments'] }}</div>
    </div>
    
    <div class="card">
        <div class="text-body-s text-text-muted mb-2">{{ __('Open Tickets') }}</div>
        <div class="text-h2 font-heading text-error">{{ $stats['open_tickets'] }}</div>
    </div>
</div>

<!-- Activity Chart -->
<div class="card mb-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-h3 font-heading">{{ __('Activity Overview') }}</h2>
        <span class="text-body-s text-text-muted">{{ __('Last 6 months') }}</span>
    </div>
    <div class="h-64">
        <canvas id="activityChart"></canvas>
    </div>
</div>

<!-- Recent Activity -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Recent Inbounds -->
    <div class="card">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-h3 font-heading">{{ __('Recent Inbounds') }}</h2>
            <a href="{{ route('cabinet.inbounds.index') }}" class="text-brand hover:underline">{{ __('View all') }}</a>
        </div>
        
        @forelse($recentInbounds as $inbound)
        <div class="flex justify-between items-center p-4 bg-bg-soft rounded-btn mb-2">
            <div>
                <div class="font-semibold">{{ $inbound->reference }}</div>
                <div class="text-body-s text-text-muted">{{ $inbound->created_at->format('d.m.Y H:i') }}</div>
            </div>
            <span class="badge badge-{{ $inbound->status === 'received' ? 'success' : ($inbound->status === 'draft' ? 'warning' : 'info') }}">
                {{ ucfirst($inbound->status) }}
            </span>
        </div>
        @empty
        <p class="text-text-muted text-center py-8">{{ __('No inbounds yet') }}</p>
        @endforelse
    </div>
    
    <!-- Recent Shipments -->
    <div class="card">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-h3 font-heading">{{ __('Recent Shipments') }}</h2>
            <a href="{{ route('cabinet.shipments.index') }}" class="text-brand hover:underline">{{ __('View all') }}</a>
        </div>
        
        @forelse($recentShipments as $shipment)
        <div class="flex justify-between items-center p-4 bg-bg-soft rounded-btn mb-2">
            <div>
                <div class="font-semibold">{{ ucfirst($shipment->marketplace) }} - {{ $shipment->warehouse_name }}</div>
                <div class="text-body-s text-text-muted">{{ $shipment->created_at->format('d.m.Y H:i') }}</div>
            </div>
            <span class="badge badge-{{ $shipment->status === 'shipped' ? 'success' : ($shipment->status === 'draft' ? 'warning' : 'info') }}">
                {{ ucfirst($shipment->status) }}
            </span>
        </div>
        @empty
        <p class="text-text-muted text-center py-8">{{ __('No shipments yet') }}</p>
        @endforelse
    </div>
</div>

<!-- Quick Actions -->
<div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
    <a href="{{ route('cabinet.inbounds.create') }}" class="card hover:shadow-lg transition text-center">
        <svg class="w-12 h-12 text-brand mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        <h3 class="text-h4 font-heading mb-2">{{ __('Create Inbound') }}</h3>
        <p class="text-body-s text-text-muted">{{ __('Plan new product arrival') }}</p>
    </a>
    
    <a href="{{ route('cabinet.shipments.create') }}" class="card hover:shadow-lg transition text-center">
        <svg class="w-12 h-12 text-brand mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
        </svg>
        <h3 class="text-h4 font-heading mb-2">{{ __('Create Shipment') }}</h3>
        <p class="text-body-s text-text-muted">{{ __('Send products to marketplace') }}</p>
    </a>
    
    <a href="{{ route('cabinet.tickets.create') }}" class="card hover:shadow-lg transition text-center">
        <svg class="w-12 h-12 text-brand mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
        </svg>
        <h3 class="text-h4 font-heading mb-2">{{ __('Create Ticket') }}</h3>
        <p class="text-body-s text-text-muted">{{ __('Contact support') }}</p>
    </a>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('activityChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($chartData['labels']),
                datasets: [
                    {
                        label: '{{ __("Shipments") }}',
                        data: @json($chartData['shipments']),
                        backgroundColor: 'rgba(99, 102, 241, 0.8)',
                        borderColor: 'rgb(99, 102, 241)',
                        borderWidth: 1,
                        borderRadius: 6,
                    },
                    {
                        label: '{{ __("Inbounds") }}',
                        data: @json($chartData['inbounds']),
                        backgroundColor: 'rgba(16, 185, 129, 0.8)',
                        borderColor: 'rgb(16, 185, 129)',
                        borderWidth: 1,
                        borderRadius: 6,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
@endsection
