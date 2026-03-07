@extends('layouts.app')

@section('title', __('About') . ' - RISMENT')

@section('content')
<section class="gradient-brand text-white py-20">
    <div class="container-risment text-center">
        <h1 class="text-h1 font-heading mb-6">
            {{ __('About RISMENT') }}
        </h1>
        <p class="text-body-l max-w-3xl mx-auto opacity-90">
            {{ __('Professional fulfillment for Uzbekistan marketplaces') }}
        </p>
    </div>
</section>

<section class="py-16">
    <div class="container-risment max-w-4xl">
        <div class="prose max-w-none mb-12">
            <h2 class="text-h2 font-heading mb-6">
                {{ __('Mission') }}
            </h2>
            <p class="text-body-m text-text-muted mb-6">
                {{ __('RISMENT is a modern fulfillment center specializing in serving marketplace sellers in Uzbekistan. We help businesses scale by taking on all logistics tasks: from storing goods to packaging and delivery.') }}
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            <div class="card text-center">
                <div class="text-4xl font-bold text-brand mb-2">1000+</div>
                <div class="text-body-s text-text-muted">
                    {{ __('Orders per day') }}
                </div>
            </div>
            <div class="card text-center">
                <div class="text-4xl font-bold text-brand mb-2">99.9%</div>
                <div class="text-body-s text-text-muted">
                    {{ __('Packaging accuracy') }}
                </div>
            </div>
            <div class="card text-center">
                <div class="text-4xl font-bold text-brand mb-2">24/7</div>
                <div class="text-body-s text-text-muted">
                    {{ __('Support') }}
                </div>
            </div>
        </div>

        <div class="mb-12">
            <h2 class="text-h2 font-heading mb-6">
                {{ __('Our Advantages') }}
            </h2>
            <div class="space-y-4">
                <div class="card">
                    <h3 class="text-h4 font-heading mb-2">
                        {{ __('Transparency') }}
                    </h3>
                    <p class="text-body-m text-text-muted">
                        {{ __('Personal dashboard with real-time data access') }}
                    </p>
                </div>
                <div class="card">
                    <h3 class="text-h4 font-heading mb-2">
                        {{ __('Reliability') }}
                    </h3>
                    <p class="text-body-m text-text-muted">
                        {{ __('SLA for every process with financial guarantees') }}
                    </p>
                </div>
                <div class="card">
                    <h3 class="text-h4 font-heading mb-2">
                        {{ __('Technology') }}
                    </h3>
                    <p class="text-body-m text-text-muted">
                        {{ __('Modern warehouse equipment and WMS system') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="card gradient-brand text-white text-center p-8">
            <h2 class="text-h2 font-heading mb-4">
                {{ __('Ready to start working?') }}
            </h2>
            <p class="text-body-l mb-6 opacity-90">
                {{ __('Contact us for consultation') }}
            </p>
            <a href="{{ route('contacts', ['locale' => app()->getLocale()]) }}" class="btn btn-primary bg-white text-brand">
                {{ __('Contacts') }}
            </a>
        </div>
    </div>
</section>
@endsection
