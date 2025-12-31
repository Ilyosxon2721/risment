@extends('cabinet.layout')

@section('title', __('Choose Your Package'))

@section('content')
<div class="mb-8">
    <h1 class="text-h1 font-heading">{{ __('Choose Your Package') }}</h1>
    <p class="text-body-m text-text-muted mt-2">{{ __('Select the plan that best fits your business needs') }}</p>
</div>

<!-- Packages Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
    @foreach($plans as $plan)
    <div class="card relative {{ $currentSubscription && $currentSubscription->plan_id == $plan->id ? 'border-2 border-brand' : '' }} {{ $plan->code === 'pro' ? 'shadow-lg' : '' }}">
        <!-- Most Popular Badge -->
        @if($plan->code === 'pro')
        <div class="absolute -top-3 left-1/2 transform -translate-x-1/2">
            <span class="px-4 py-1 bg-brand text-white text-body-s font-semibold rounded-full">
                {{ __('Most Popular') }}
            </span>
        </div>
        @endif
        
        <!-- Current Plan Badge -->
        @if($currentSubscription && $currentSubscription->plan_id == $plan->id)
        <div class="absolute -top-3 right-4">
            <span class="px-3 py-1 bg-success text-white text-body-s font-semibold rounded-full">
                {{ __('Current Plan') }}
            </span>
        </div>
        @endif
        
        <!-- Package Header -->
        <div class="text-center mb-6">
            <h3 class="text-h3 font-heading mb-2">{{ $plan->getName() }}</h3>
            <p class="text-body-s text-text-muted mb-4">{{ $plan->getDescription() }}</p>
            <div class="text-h2 text-brand font-heading">
                {{ number_format($plan->price_month, 0, '', ' ') }} <span class="text-body-m">{{ __('UZS') }}</span>
            </div>
            <div class="text-body-s text-text-muted">{{ __('per month') }}</div>
        </div>
        
        <!-- Features List -->
        <div class="mb-6">
            <h4 class="text-body-m font-semibold mb-3">{{ __('What\'s included') }}:</h4>
            <ul class="space-y-2">
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-success flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-body-s">{{ $plan->fbs_shipments_included }} {{ __('FBS shipments') }}</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-success flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-body-s">{{ $plan->storage_included_boxes }} {{ __('boxes storage') }}</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-success flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-body-s">{{ $plan->storage_included_bags }} {{ __('bags storage') }}</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-success flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-body-s">{{ $plan->inbound_included_boxes }} {{ __('inbound boxes') }}</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-success flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-body-s">{{ __('Pick & pack included') }}</span>
                </li>
            </ul>
        </div>
        
        <!-- Action Button -->
        @if($currentSubscription && $currentSubscription->plan_id == $plan->id)
            <button disabled class="btn btn-secondary w-full opacity-50 cursor-not-allowed">
                {{ __('Current Plan') }}
            </button>
        @else
            <form action="{{ route('cabinet.subscription.select', $plan->id) }}" method="POST">
                @csrf
                <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                <button type="submit" class="btn {{ $plan->code === 'pro' ? 'btn-primary' : 'btn-secondary' }} w-full">
                    {{ __('Select Plan') }}
                </button>
            </form>
        @endif
    </div>
    @endforeach
</div>

<!-- Back to Dashboard -->
<div class="mt-8 text-center">
    <a href="{{ route('cabinet.dashboard') }}" class="text-brand hover:underline">
        ← {{ __('Back to Dashboard') }}
    </a>
</div>
@endsection
