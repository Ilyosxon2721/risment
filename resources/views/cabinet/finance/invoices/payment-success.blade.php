@extends('cabinet.layout')

@section('title', __('Payment Successful'))

@section('content')
<div class="max-w-2xl mx-auto text-center">
    <div class="card">
        <!-- Success Icon -->
        <div class="inline-flex items-center justify-center w-20 h-20 bg-success/10 rounded-full mb-6">
            <svg class="w-12 h-12 text-success" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
        </div>
        
        <h1 class="text-h1 font-heading mb-4">{{ __('Payment Successful!') }}</h1>
        <p class="text-body-m text-text-muted mb-8">
            {{ __('Your payment has been processed successfully. The invoice has been marked as paid.') }}
        </p>
        
        <div class="flex justify-center gap-4">
            <a href="{{ route('cabinet.dashboard') }}" class="btn btn-secondary">
                {{ __('Go to Dashboard') }}
            </a>
            <a href="{{ route('cabinet.finance.invoices.index') }}" class="btn btn-primary">
                {{ __('View Invoices') }}
            </a>
        </div>
    </div>
</div>
@endsection
