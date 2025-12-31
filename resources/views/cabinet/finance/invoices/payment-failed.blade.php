@extends('cabinet.layout')

@section('title', __('Payment Failed'))

@section('content')
<div class="max-w-2xl mx-auto text-center">
    <div class="card">
        <!-- Error Icon -->
        <div class="inline-flex items-center justify-center w-20 h-20 bg-error/10 rounded-full mb-6">
            <svg class="w-12 h-12 text-error" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
        </div>
        
        <h1 class="text-h1 font-heading mb-4">{{ __('Payment Failed') }}</h1>
        <p class="text-body-m text-text-muted mb-8">
            {{ __('Unfortunately, your payment could not be processed. Please try again or contact support if the problem persists.') }}
        </p>
        
        <div class="flex justify-center gap-4">
            <a href="{{ route('cabinet.dashboard') }}" class="btn btn-secondary">
                {{ __('Go to Dashboard') }}
            </a>
            <a href="{{ route('cabinet.finance.invoices.index') }}" class="btn btn-primary">
                {{ __('Try Again') }}
            </a>
        </div>
    </div>
</div>
@endsection
