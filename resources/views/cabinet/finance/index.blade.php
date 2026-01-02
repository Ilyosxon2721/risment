@extends('cabinet.layout')

@section('title', __('Finance'))

@section('content')
<div class="mb-8">
    <h1 class="text-h1 font-heading">{{ __('Finance') }}</h1>
    <p class="text-body-m text-text-muted mt-2">{{ __('Manage invoices and payments') }}</p>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <!-- Balance -->
    <div class="card">
        <div class="text-body-s text-text-muted mb-2">{{ __('Balance') }}</div>
        <div class="text-h2 font-heading mb-1 {{ $currentBalance < 0 ? 'text-error' : 'text-success' }}">
            {{ number_format(abs($currentBalance), 0, '', ' ') }} {{ __('UZS') }}
        </div>
        <div class="text-body-s {{ $currentBalance < 0 ? 'text-error' : 'text-success' }}">
            {{ $currentBalance < 0 ? __('Debt') : __('Credit') }}
        </div>
    </div>
    
    <!-- Total Paid This Year -->
    <div class="card">
        <div class="text-body-s text-text-muted mb-2">{{ __('Paid This Year') }}</div>
        <div class="text-h2 font-heading mb-1 text-brand">
            {{ number_format($totalPaidThisYear, 0, '', ' ') }} {{ __('UZS') }}
        </div>
        <div class="text-body-s text-text-muted">{{ now()->year }}</div>
    </div>
    
    <!-- Outstanding -->
    <div class="card">
        <div class="text-body-s text-text-muted mb-2">{{ __('Outstanding') }}</div>
        <div class="text-h2 font-heading mb-1 {{ $outstandingAmount > 0 ? 'text-warning' : 'text-text' }}">
            {{ number_format($outstandingAmount, 0, '', ' ') }} {{ __('UZS') }}
        </div>
        <div class="text-body-s text-text-muted">{{ __('Unpaid invoices') }}</div>
    </div>
    
    <!-- Last Payment -->
    <div class="card">
        <div class="text-body-s text-text-muted mb-2">{{ __('Last Payment') }}</div>
        <div class="text-h3 font-heading mb-1 text-brand">
            @if($lastPaymentDate)
                {{ $lastPaymentDate->format('d.m.Y') }}
            @else
                —
            @endif
        </div>
        <div class="text-body-s text-text-muted">{{ __('Payment date') }}</div>
    </div>
</div>

<!-- Recent Data -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Recent Invoices -->
    <div class="card">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-h3 font-heading">{{ __('Recent Invoices') }}</h2>
            <a href="{{ route('cabinet.finance.invoices.index') }}" class="text-brand hover:underline text-body-s">
                {{ __('View All') }} →
            </a>
        </div>
        
        @if($recentInvoices->count() > 0)
            <div class="space-y-3">
                @foreach($recentInvoices as $invoice)
                <div class="flex justify-between items-center p-3 bg-bg-soft rounded-btn">
                    <div>
                        <div class="font-semibold text-body-m">{{ $invoice->invoice_number }}</div>
                        <div class="text-body-s text-text-muted">{{ $invoice->issue_date->format('d.m.Y') }}</div>
                    </div>
                    <div class="text-right">
                        <div class="font-semibold">{{ number_format($invoice->total, 0, '', ' ') }} {{ __('UZS') }}</div>
                        <span class="badge {{ $invoice->getStatusBadgeClass() }}">{{ $invoice->getStatusLabel() }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-text-muted py-8">{{ __('No invoices yet') }}</p>
        @endif
    </div>
    
    <!-- Recent Payments -->
    <div class="card">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-h3 font-heading">{{ __('Recent Payments') }}</h2>
            <a href="{{ route('cabinet.finance.payments') }}" class="text-brand hover:underline text-body-s">
                {{ __('View All') }} →
            </a>
        </div>
        
        @if($recentPayments->count() > 0)
            <div class="space-y-3">
                @foreach($recentPayments as $payment)
                <div class="flex justify-between items-center p-3 bg-bg-soft rounded-btn">
                    <div>
                        <div class="font-semibold text-body-m">{{ $payment->payment_date->format('d.m.Y') }}</div>
                        <div class="text-body-s text-text-muted">
                            @if($payment->invoice)
                                {{ $payment->invoice->invoice_number }}
                            @else
                                {{ __('Payment without invoice') }}
                            @endif
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="font-semibold text-success">+{{ number_format($payment->amount, 0, '', ' ') }} {{ __('UZS') }}</div>
                        <div class="text-body-s text-text-muted">{{ $payment->getMethodLabel() }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-text-muted py-8">{{ __('No payments yet') }}</p>
        @endif
    </div>
</div>
@endsection
