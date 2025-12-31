@extends('cabinet.layout')

@section('title', __('Inbounds'))

@section('content')
<div class="flex justify-between items-center mb-8">
    <h1 class="text-h1 font-heading">{{ __('Inbounds') }}</h1>
    <a href="{{ route('cabinet.inbounds.create') }}" class="btn btn-primary">{{ __('Create Inbound') }}</a>
</div>

<div class="card">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-brand-border">
                    <th class="text-left p-4">{{ __('Reference') }}</th>
                    <th class="text-left p-4">{{ __('Planned Date') }}</th>
                    <th class="text-left p-4">{{ __('Status') }}</th>
                    <th class="text-left p-4">{{ __('Created') }}</th>
                    <th class="text-right p-4">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inbounds as $inbound)
                <tr class="border-b border-brand-border hover:bg-bg-soft">
                    <td class="p-4 font-semibold">{{ $inbound->reference }}</td>
                    <td class="p-4">{{ $inbound->planned_at ? $inbound->planned_at->format('d.m.Y') : '-' }}</td>
                    <td class="p-4">
                        <span class="badge badge-{{ $inbound->status === 'received' ? 'success' : ($inbound->status === 'draft' ? 'warning' : 'info') }}">
                            {{ ucfirst($inbound->status) }}
                        </span>
                    </td>
                    <td class="p-4">{{ $inbound->created_at->format('d.m.Y H:i') }}</td>
                    <td class="p-4 text-right">
                        <a href="{{ route('cabinet.inbounds.show', $inbound) }}" class="text-brand hover:underline">{{ __('View') }}</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-8 text-center text-text-muted">{{ __('No inbounds yet') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="p-4 border-t border-brand-border">
        {{ $inbounds->links() }}
    </div>
</div>
@endsection
