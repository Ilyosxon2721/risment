@extends('cabinet.layout')

@section('title', __('Package Activated'))

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Success Icon -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-success/10 rounded-full mb-4">
            <svg class="w-8 h-8 text-success" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
        </div>
        <h1 class="text-h1 font-heading mb-2">{{ __('Package Activated!') }}</h1>
        <p class="text-body-m text-text-muted">{{ __('Your subscription plan is now active') }}</p>
    </div>

    <!-- Selected Package Summary -->
    <div class="card mb-6">
        <h2 class="text-h3 font-heading mb-4">{{ __('Active Package') }}</h2>

        <div class="p-6 bg-bg-soft rounded-btn mb-6">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="text-h4 font-heading mb-1">{{ $selectedPlan->getName() }}</h3>
                    <p class="text-body-s text-text-muted">{{ $selectedPlan->getDescription() }}</p>
                </div>
                <div class="text-right">
                    @if($discountApplied)
                    <div class="text-body-s text-text-muted line-through">{{ number_format($selectedPlan->price_month, 0, '', ' ') }} {{ __('UZS') }}</div>
                    <div class="text-h3 text-success">{{ number_format($effectivePrice, 0, '', ' ') }} {{ __('UZS') }}</div>
                    <div class="text-body-s text-success font-semibold">{{ __('per month') }} ({{ __('discount applied') }})</div>
                    @else
                    <div class="text-h3 text-brand">{{ number_format($selectedPlan->price_month, 0, '', ' ') }} {{ __('UZS') }}</div>
                    <div class="text-body-s text-text-muted">{{ __('per month') }}</div>
                    @endif
                </div>
            </div>

            <!-- Features -->
            <div class="grid grid-cols-2 gap-3 text-body-s">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-success" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ $selectedPlan->fbs_shipments_included }} {{ __('FBS shipments') }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-success" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ $selectedPlan->storage_included_boxes }} {{ __('boxes storage') }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-success" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ $selectedPlan->storage_included_bags }} {{ __('bags storage') }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-success" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ $selectedPlan->inbound_included_boxes }} {{ __('inbound boxes') }}</span>
                </div>
            </div>
        </div>

        @if($selectedPlan->price_month > 0)
        <div class="p-4 bg-green-50 border border-green-200 rounded-btn">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-success flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <div class="font-semibold text-green-900">{{ __('Plan is active for 30 days') }}</div>
                    <div class="text-body-s text-green-800 mt-1">{{ __('Valid until') }}: {{ now()->addMonth()->format('d.m.Y') }}</div>
                </div>
            </div>
        </div>
        @else
        <div class="p-4 bg-green-50 border border-green-200 rounded-btn">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-success flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <div class="font-semibold text-green-900">{{ __('Pay-as-you-go plan is active') }}</div>
                    <div class="text-body-s text-green-800 mt-1">{{ __('Services are charged per unit, no monthly fee') }}</div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Actions -->
    <div class="flex justify-center gap-4">
        <a href="{{ route('cabinet.dashboard') }}" class="btn btn-primary">
            {{ __('Go to Dashboard') }}
        </a>
        <a href="{{ route('cabinet.subscription.choose') }}" class="btn btn-secondary">
            {{ __('View Other Plans') }}
        </a>
    </div>
</div>
@endsection
