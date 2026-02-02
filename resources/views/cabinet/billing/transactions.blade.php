@extends('cabinet.layout')

@section('title', __('Transaction History'))

@section('content')
<div class="mb-8">
    <a href="{{ route('cabinet.billing.report') }}" class="text-brand hover:underline text-body-s">&larr; {{ __('Back to Billing') }}</a>
    <h1 class="text-h1 font-heading mt-4">{{ __('Transaction History') }}</h1>
</div>

<div class="card">
    @if($transactions->count())
        <table class="w-full">
            <thead>
                <tr class="border-b border-brand-border text-left text-body-s text-text-muted">
                    <th class="pb-3">{{ __('Date') }}</th>
                    <th class="pb-3">{{ __('Type') }}</th>
                    <th class="pb-3">{{ __('Description') }}</th>
                    <th class="pb-3 text-right">{{ __('Amount') }}</th>
                    <th class="pb-3 text-right">{{ __('Balance After') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $tx)
                <tr class="border-b border-brand-border/50">
                    <td class="py-3 text-body-s">{{ $tx->created_at->format('d.m.Y H:i') }}</td>
                    <td class="py-3"><span class="badge {{ $tx->getTypeBadgeClass() }}">{{ $tx->getTypeLabel() }}</span></td>
                    <td class="py-3">{{ $tx->description }}</td>
                    <td class="py-3 text-right font-semibold {{ $tx->amount >= 0 ? 'text-success' : 'text-error' }}">
                        {{ $tx->amount >= 0 ? '+' : '' }}{{ number_format($tx->amount, 0, '', ' ') }}
                    </td>
                    <td class="py-3 text-right text-body-s text-text-muted">
                        {{ number_format($tx->balance_after, 0, '', ' ') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-6">
            {{ $transactions->links() }}
        </div>
    @else
        <p class="text-center text-text-muted py-8">{{ __('No transactions yet') }}</p>
    @endif
</div>
@endsection
