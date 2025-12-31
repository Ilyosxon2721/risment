@extends('cabinet.layout')

@section('title', __('Pay Invoice') . ' #' . $invoice->invoice_number)

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-8">
        <h1 class="text-h1 font-heading">{{ __('Pay Invoice') }}</h1>
        <p class="text-body-m text-text-muted mt-2">{{ __('Choose your payment method') }}</p>
    </div>
    
    <!-- Invoice Summary -->
    <div class="card mb-8">
        <h2 class="text-h3 font-heading mb-4">{{ __('Invoice Details') }}</h2>
        
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <div class="text-body-s text-text-muted">{{ __('Invoice Number') }}</div>
                <div class="font-semibold">{{ $invoice->invoice_number }}</div>
            </div>
            <div>
                <div class="text-body-s text-text-muted">{{ __('Status') }}</div>
                <span class="badge badge-{{ $invoice->getStatusBadgeClass() }}">
                    {{ $invoice->getStatusLabel() }}
                </span>
            </div>
            <div>
                <div class="text-body-s text-text-muted">{{ __('Issue Date') }}</div>
                <div class="font-semibold">{{ $invoice->issue_date->format('d.m.Y') }}</div>
            </div>
            <div>
                <div class="text-body-s text-text-muted">{{ __('Due Date') }}</div>
                <div class="font-semibold">{{ $invoice->due_date ? $invoice->due_date->format('d.m.Y') : '-' }}</div>
            </div>
        </div>
        
        <div class="border-t border-brand-border pt-4">
            <div class="flex justify-between items-center">
                <span class="text-h4 font-heading">{{ __('Total Amount') }}</span>
                <span class="text-h2 text-brand font-heading">{{ number_format($invoice->total, 0, '', ' ') }} {{ __('UZS') }}</span>
            </div>
        </div>
    </div>
    
    <!-- Payment Methods -->
    <div class="card mb-8">
        <h2 class="text-h3 font-heading mb-6">{{ __('Select Payment Method') }}</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Click Payment -->
            <form action="{{ route('cabinet.finance.invoices.pay.click', $invoice) }}" method="POST">
                @csrf
                <button type="submit" class="w-full p-6 border-2 border-gray-200 rounded-btn hover:border-brand hover:shadow-lg transition group">
                    <div class="flex flex-col items-center">
                        <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mb-4 group-hover:bg-blue-200 transition">
                            <svg class="w-12 h-12 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                        </div>
                        <h3 class="text-h4 font-heading mb-2">Click</h3>
                        <p class="text-body-s text-text-muted text-center">{{ __('Pay with bank card via Click') }}</p>
                    </div>
                </button>
            </form>
            
            <!-- Payme Payment -->
            <form action="{{ route('cabinet.finance.invoices.pay.payme', $invoice) }}" method="POST">
                @csrf
                <button type="submit" class="w-full p-6 border-2 border-gray-200 rounded-btn hover:border-brand hover:shadow-lg transition group">
                    <div class="flex flex-col items-center">
                        <div class="w-20 h-20 bg-purple-100 rounded-full flex items-center justify-center mb-4 group-hover:bg-purple-200 transition">
                            <svg class="w-12 h-12 text-purple-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/>
                            </svg>
                        </div>
                        <h3 class="text-h4 font-heading mb-2">Payme</h3>
                        <p class="text-body-s text-text-muted text-center">{{ __('Pay via Payme mobile app') }}</p>
                    </div>
                </button>
            </form>
        </div>
    </div>
    
    <!-- Back Link -->
    <div class="text-center">
        <a href="{{ route('cabinet.finance.invoices.show', $invoice) }}" class="text-brand hover:underline">
            ← {{ __('Back to Invoice') }}
        </a>
    </div>
</div>
@endsection
