@extends('manager.layout')

@section('title', __('Inbound') . ' #' . $inbound->id)

@section('content')
<div class="mb-8">
    <a href="{{ route('manager.inbounds.index') }}" class="text-brand hover:underline text-body-s">&larr; {{ __('Back to list') }}</a>
    <h2 class="text-h2 font-heading mt-4">{{ __('Inbound') }} #{{ $inbound->id }}</h2>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Info -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-card border border-brand-border p-6 mb-6">
            <h3 class="text-h4 font-heading mb-4">{{ __('Information') }}</h3>
            <dl class="grid grid-cols-2 gap-4">
                <div>
                    <dt class="text-body-s text-text-muted">{{ __('Created date') }}</dt>
                    <dd>{{ $inbound->created_at->format('d.m.Y H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-body-s text-text-muted">{{ __('Expected date') }}</dt>
                    <dd>{{ $inbound->expected_at ? $inbound->expected_at->format('d.m.Y') : '-' }}</dd>
                </div>
                @if($inbound->received_at)
                <div>
                    <dt class="text-body-s text-text-muted">{{ __('Receiving date') }}</dt>
                    <dd>{{ $inbound->received_at->format('d.m.Y H:i') }}</dd>
                </div>
                @endif
            </dl>
            @if($inbound->notes)
            <div class="mt-4 pt-4 border-t border-brand-border">
                <dt class="text-body-s text-text-muted">{{ __('Notes') }}</dt>
                <dd class="mt-1">{{ $inbound->notes }}</dd>
            </div>
            @endif
        </div>

        <!-- Items -->
        <div class="bg-white rounded-card border border-brand-border p-6">
            <h3 class="text-h4 font-heading mb-4">{{ __('Products') }}</h3>

            @if(in_array($inbound->status, ['draft', 'submitted']))
            <form method="POST" action="{{ route('manager.inbounds.receive', $inbound) }}">
                @csrf
                <table class="w-full">
                    <thead class="bg-bg-soft">
                        <tr>
                            <th class="px-4 py-2 text-left text-body-s font-semibold text-text-muted">SKU</th>
                            <th class="px-4 py-2 text-left text-body-s font-semibold text-text-muted">{{ __('Name') }}</th>
                            <th class="px-4 py-2 text-right text-body-s font-semibold text-text-muted">{{ __('Expected') }}</th>
                            <th class="px-4 py-2 text-right text-body-s font-semibold text-text-muted">{{ __('Received') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-brand-border">
                        @foreach($inbound->items as $item)
                        <tr>
                            <td class="px-4 py-3 font-mono text-body-s">{{ $item->sku->sku ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $item->sku->name ?? __('No name') }}</td>
                            <td class="px-4 py-3 text-right">{{ $item->qty }}</td>
                            <td class="px-4 py-3 text-right">
                                <input type="hidden" name="items[{{ $loop->index }}][id]" value="{{ $item->id }}">
                                <input type="number" name="items[{{ $loop->index }}][received_qty]" value="{{ $item->qty }}" min="0" class="input w-24 text-right">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-6">
                    <button type="submit" class="btn-brand px-6 py-3 rounded-btn text-white font-semibold">
                        {{ __('Confirm receiving') }}
                    </button>
                </div>
            </form>
            @else
            <table class="w-full">
                <thead class="bg-bg-soft">
                    <tr>
                        <th class="px-4 py-2 text-left text-body-s font-semibold text-text-muted">SKU</th>
                        <th class="px-4 py-2 text-left text-body-s font-semibold text-text-muted">{{ __('Name') }}</th>
                        <th class="px-4 py-2 text-right text-body-s font-semibold text-text-muted">{{ __('Expected') }}</th>
                        <th class="px-4 py-2 text-right text-body-s font-semibold text-text-muted">{{ __('Received') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-border">
                    @foreach($inbound->items as $item)
                    @php
                        $diff = ($item->received_qty ?? 0) - $item->qty;
                        $diffClass = $diff < 0 ? 'text-error' : ($diff > 0 ? 'text-success' : '');
                    @endphp
                    <tr>
                        <td class="px-4 py-3 font-mono text-body-s">{{ $item->sku->sku ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $item->sku->name ?? __('No name') }}</td>
                        <td class="px-4 py-3 text-right">{{ $item->qty }}</td>
                        <td class="px-4 py-3 text-right font-semibold {{ $diffClass }}">
                            {{ $item->received_qty ?? '-' }}
                            @if($diff != 0)
                                <span class="text-body-xs">({{ $diff > 0 ? '+' : '' }}{{ $diff }})</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>

    <!-- Status Panel -->
    <div>
        <div class="bg-white rounded-card border border-brand-border p-6">
            <h3 class="text-h4 font-heading mb-4">{{ __('Status') }}</h3>

            @php
                $statusColors = [
                    'draft' => 'gray',
                    'submitted' => 'yellow',
                    'received' => 'green',
                    'cancelled' => 'red',
                ];
                $statusLabels = [
                    'draft' => __('Draft'),
                    'submitted' => __('Pending'),
                    'received' => __('Received'),
                    'cancelled' => __('Cancelled'),
                ];
            @endphp

            <div class="mb-4">
                <span class="inline-flex items-center px-3 py-2 rounded-full text-sm font-semibold bg-{{ $statusColors[$inbound->status] ?? 'gray' }}-100 text-{{ $statusColors[$inbound->status] ?? 'gray' }}-800">
                    {{ $statusLabels[$inbound->status] ?? $inbound->status }}
                </span>
            </div>

            @if($inbound->status === 'received')
            <p class="text-body-s text-text-muted">
                {{ __('Receiving completed') }} {{ $inbound->received_at->format('d.m.Y H:i') }}
            </p>
            @elseif(in_array($inbound->status, ['draft', 'submitted']))
            <p class="text-body-s text-text-muted">
                {{ __('Fill in the received quantities and click "Confirm receiving"') }}
            </p>
            @endif
        </div>
    </div>
</div>
@endsection
