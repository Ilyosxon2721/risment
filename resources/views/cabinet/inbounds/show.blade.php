@extends('cabinet.layout')

@section('title', __('Inbound') . ' ' . $inbound->reference)

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-h1 font-heading">{{ __('Inbound') }} #{{ $inbound->reference }}</h1>
            <p class="text-body-m text-text-muted mt-2">{{ __('Created') }} {{ $inbound->created_at->format('d.m.Y H:i') }}</p>
        </div>
        <div class="flex gap-3">
            @if($inbound->status === 'draft')
            <a href="{{ route('cabinet.inbounds.edit', ['inbound' => $inbound]) }}" class="btn btn-secondary">
                {{ __('Edit') }}
            </a>
            @endif
            <a href="{{ route('cabinet.inbounds.index') }}" class="btn btn-ghost">
                ← {{ __('Back') }}
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Main Content -->
    <div class="lg:col-span-2">
        <!-- Information Card -->
        <div class="card mb-6">
            <h2 class="text-h3 font-heading mb-6">{{ __('Inbound Information') }}</h2>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <div class="text-body-s text-text-muted mb-1">{{ __('Reference') }}</div>
                    <div class="font-semibold">{{ $inbound->reference }}</div>
                </div>
                
                <div>
                    <div class="text-body-s text-text-muted mb-1">{{ __('Status') }}</div>
                    <div>
                        <span class="badge badge-{{ 
                            $inbound->status === 'draft' ? 'warning' : 
                            ($inbound->status === 'received' ? 'success' : 'info') 
                        }}">
                            {{ __(ucfirst($inbound->status)) }}
                        </span>
                    </div>
                </div>
                
                @if($inbound->planned_at)
                <div>
                    <div class="text-body-s text-text-muted mb-1">{{ __('Planned Date') }}</div>
                    <div class="font-semibold">{{ \Carbon\Carbon::parse($inbound->planned_at)->format('d.m.Y') }}</div>
                </div>
                @endif
                
                @if($inbound->received_at)
                <div>
                    <div class="text-body-s text-text-muted mb-1">{{ __('Received Date') }}</div>
                    <div class="font-semibold">{{ \Carbon\Carbon::parse($inbound->received_at)->format('d.m.Y H:i') }}</div>
                </div>
                @endif
            </div>
            
            @if($inbound->notes)
            <div class="mt-6 pt-6 border-t border-brand-border">
                <div class="text-body-s text-text-muted mb-2">{{ __('Notes') }}</div>
                <div class="text-body-m">{{ $inbound->notes }}</div>
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
                            <th class="px-4 py-3 text-right text-body-s font-semibold">{{ __('Planned') }}</th>
                            <th class="px-4 py-3 text-right text-body-s font-semibold">{{ __('Received') }}</th>
                            <th class="px-4 py-3 text-left text-body-s font-semibold">{{ __('Notes') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inbound->items as $item)
                        <tr class="border-t border-brand-border">
                            <td class="px-4 py-3 font-mono text-body-s">{{ $item->sku->sku }}</td>
                            <td class="px-4 py-3">{{ $item->sku->name }}</td>
                            <td class="px-4 py-3 text-right font-semibold">{{ number_format($item->qty_planned) }}</td>
                            <td class="px-4 py-3 text-right font-semibold {{ $item->qty_received ? 'text-success' : 'text-text-muted' }}">
                                {{ $item->qty_received ? number_format($item->qty_received) : '-' }}
                            </td>
                            <td class="px-4 py-3 text-body-s text-text-muted">{{ $item->notes ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-bg-soft border-t-2 border-brand-dark">
                        <tr>
                            <td colspan="2" class="px-4 py-3 font-semibold">{{ __('Total') }}</td>
                            <td class="px-4 py-3 text-right font-semibold">
                                {{ number_format($inbound->items->sum('qty_planned')) }}
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-success">
                                {{ number_format($inbound->items->sum('qty_received')) }}
                            </td>
                            <td></td>
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
                    <span class="font-semibold">{{ $inbound->items->count() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-text-muted">{{ __('Total Units Planned') }}</span>
                    <span class="font-semibold">{{ number_format($inbound->items->sum('qty_planned')) }}</span>
                </div>
                @if($inbound->items->sum('qty_received') > 0)
                <div class="flex justify-between">
                    <span class="text-text-muted">{{ __('Total Units Received') }}</span>
                    <span class="font-semibold text-success">{{ number_format($inbound->items->sum('qty_received')) }}</span>
                </div>
                @endif
            </div>
            
            @if($inbound->status === 'draft')
            <div class="border-t border-brand-border pt-4 mt-4">
                <a href="{{ route('cabinet.inbounds.edit', ['inbound' => $inbound]) }}" class="btn btn-primary w-full">
                    {{ __('Edit Inbound') }}
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
