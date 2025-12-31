@extends('cabinet.layout')

@section('title', __('Payment History'))

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-h1 font-heading">{{ __('Payment History') }}</h1>
            <p class="text-body-m text-text-muted mt-2">{{ __('All your payments') }}</p>
        </div>
        <a href="{{ route('cabinet.finance.index') }}" class="btn btn-secondary">
            ← {{ __('Back') }}
        </a>
    </div>
</div>

@if($payments->count() > 0)
<div class="card overflow-hidden">
    <table class="table">
        <thead>
            <tr>
                <th>{{ __('Payment Date') }}</th>
                <th>{{ __('Invoice') }}</th>
                <th>{{ __('Amount') }}</th>
                <th>{{ __('Payment Method') }}</th>
                <th>{{ __('Reference') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $payment)
            <tr>
                <td class="font-semibold">{{ $payment->payment_date->format('d.m.Y') }}</td>
                <td>
                    @if($payment->invoice)
                        <a href="{{ route('cabinet.finance.invoice', ['invoice' => $payment->invoice_id]) }}" class="text-brand hover:underline">
                            {{ $payment->invoice->invoice_number }}
                        </a>
                    @else
                        <span class="text-text-muted">{{ __('No invoice') }}</span>
                    @endif
                </td>
                <td class="font-semibold text-success">
                    {{ number_format($payment->amount, 0, '', ' ') }} {{ __('UZS') }}
                </td>
                <td>{{ $payment->getMethodLabel() }}</td>
                <td>{{ $payment->reference ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="mt-6">
    {{ $payments->links() }}
</div>

@else
<!-- Empty State -->
<div class="card text-center py-12">
    <svg class="w-16 h-16 text-text-muted mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
    </svg>
    <h3 class="text-h4 font-heading mb-2">{{ __('No payments yet') }}</h3>
    <p class="text-body-m text-text-muted mb-4">{{ __('Payment history will appear here') }}</p>
</div>
@endif
@endsection
