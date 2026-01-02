@extends('cabinet.layout')

@section('title', __('Invoice') . ' ' . $invoice->invoice_number)

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-h1 font-heading">{{ __('Invoice') }} {{ $invoice->invoice_number }}</h1>
            <p class="text-body-m text-text-muted mt-2">{{ __('Invoice details') }}</p>
        </div>
        <div class="flex gap-3">
            @if($invoice->status !== 'paid')
            <a href="{{ route('cabinet.finance.invoices.pay', $invoice) }}" class="btn btn-primary">
                {{ __('Pay Invoice') }}
            </a>
            @endif
            <a href="{{ route('cabinet.finance.invoices') }}" class="btn btn-secondary">
                ← {{ __('Back') }}
            </a>
        </div>
    </div>
</div>

<!-- Invoice Header -->
<div class="card mb-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div>
            <h3 class="text-h4 font-heading mb-4">{{ __('Invoice Information') }}</h3>
            <div class="space-y-2 text-body-m">
                <div class="flex justify-between">
                    <span class="text-text-muted">{{ __('Invoice Number') }}:</span>
                    <span class="font-semibold">{{ $invoice->invoice_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-text-muted">{{ __('Issue Date') }}:</span>
                    <span>{{ $invoice->issue_date->format('d.m.Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-text-muted">{{ __('Due Date') }}:</span>
                    <span>{{ $invoice->due_date->format('d.m.Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-text-muted">{{ __('Status') }}:</span>
                    <span class="badge {{ $invoice->getStatusBadgeClass() }}">{{ $invoice->getStatusLabel() }}</span>
                </div>
            </div>
        </div>
        
        <div class="md:text-right">
            <h3 class="text-h4 font-heading mb-4">{{ __('Amount') }}</h3>
            <div class="text-h2 font-heading text-brand">
                {{ number_format($invoice->total, 0, '', ' ') }} {{ __('UZS') }}
            </div>
            @if($invoice->getPaidAmount() > 0)
            <div class="text-body-m text-success mt-2">
                {{ __('Paid') }}: {{ number_format($invoice->getPaidAmount(), 0, '', ' ') }} {{ __('UZS') }}
            </div>
            <div class="text-body-m text-warning">
                {{ __('Remaining') }}: {{ number_format($invoice->getRemainingAmount(), 0, '', ' ') }} {{ __('UZS') }}
            </div>
            @endif
        </div>
    </div>
    
    @if($invoice->notes)
    <div class="pt-4 border-t border-brand-border">
        <h4 class="text-body-m font-semibold mb-2">{{ __('Notes') }}</h4>
        <p class="text-body-m text-text-muted">{{ $invoice->notes }}</p>
    </div>
    @endif
</div>

<!-- Invoice Items -->
<div class="card mb-6">
    <h3 class="text-h3 font-heading mb-4">{{ __('Items') }}</h3>
    
    <table class="table">
        <thead>
            <tr>
                <th>{{ __('Description') }}</th>
                <th class="text-right">{{ __('Quantity') }}</th>
                <th class="text-right">{{ __('Unit Price') }}</th>
                <th class="text-right">{{ __('Total') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
            <tr>
                <td>
                    <div class="font-semibold">{{ $item->description }}</div>
                    @if($item->service_type)
                    <div class="text-body-s text-text-muted">{{ ucfirst(str_replace('_', ' ', $item->service_type)) }}</div>
                    @endif
                </td>
                <td class="text-right">{{ $item->quantity }}</td>
                <td class="text-right">{{ number_format($item->unit_price, 0, '', ' ') }} {{ __('UZS') }}</td>
                <td class="text-right font-semibold">{{ number_format($item->total_price, 0, '', ' ') }} {{ __('UZS') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <!-- Totals -->
    <div class="mt-6 flex justify-end">
        <div class="w-full md:w-1/3 space-y-2">
            <div class="flex justify-between text-body-m">
                <span class="text-text-muted">{{ __('Subtotal') }}:</span>
                <span>{{ number_format($invoice->subtotal, 0, '', ' ') }} {{ __('UZS') }}</span>
            </div>
            @if($invoice->tax > 0)
            <div class="flex justify-between text-body-m">
                <span class="text-text-muted">{{ __('Tax') }}:</span>
                <span>{{ number_format($invoice->tax, 0, '', ' ') }} {{ __('UZS') }}</span>
            </div>
            @endif
            <div class="flex justify-between text-h4 font-heading border-t border-brand-border pt-2">
                <span>{{ __('Total') }}:</span>
                <span class="text-brand">{{ number_format($invoice->total, 0, '', ' ') }} {{ __('UZS') }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Payment History -->
@if($invoice->payments->count() > 0)
<div class="card">
    <h3 class="text-h3 font-heading mb-4">{{ __('Payment History') }}</h3>
    
    <table class="table">
        <thead>
            <tr>
                <th>{{ __('Date') }}</th>
                <th>{{ __('Amount') }}</th>
                <th>{{ __('Method') }}</th>
                <th>{{ __('Reference') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->payments as $payment)
            <tr>
                <td>{{ $payment->payment_date->format('d.m.Y') }}</td>
                <td class="font-semibold text-success">{{ number_format($payment->amount, 0, '', ' ') }} {{ __('UZS') }}</td>
                <td>{{ $payment->getMethodLabel() }}</td>
                <td>{{ $payment->reference ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
@endsection
