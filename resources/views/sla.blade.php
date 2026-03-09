@extends('layouts.app')

@section('title', __('SLA') . ' - RISMENT')

@section('content')
<section class="py-16">
    <div class="container-risment max-w-4xl">
        <h1 class="text-2xl sm:text-h1 font-heading text-center mb-8 sm:mb-12">
{{ __('SLA and Regulations') }}
        </h1>
        
        <div class="prose max-w-none">
            <div class="card mb-8">
                <h2 class="text-h3 font-heading mb-4">
{{ __('Processing Times') }}
                </h2>
                <ul class="space-y-3">
                    <li class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-1 p-3 bg-bg-soft rounded-btn">
                        <span>
{{ __('Goods Receiving') }}
                        </span>
                        <span class="font-semibold text-brand">
{{ __('1-2 business days') }}
                        </span>
                    </li>
                    <li class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-1 p-3 bg-bg-soft rounded-btn">
                        <span>
{{ __('FBS/DBS Order Assembly') }}
                        </span>
                        <span class="font-semibold text-brand">
{{ __('Same day') }}
                        </span>
                    </li>
                    <li class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-1 p-3 bg-bg-soft rounded-btn">
                        <span>
{{ __('FBO Shipment Preparation') }}
                        </span>
                        <span class="font-semibold text-brand">
{{ __('2-3 business days') }}
                        </span>
                    </li>
                </ul>
            </div>
            
            <div class="card mb-8">
                <h2 class="text-h3 font-heading mb-4">
{{ __('Packaging Requirements') }}
                </h2>
                <p class="text-body-m text-text-muted mb-4">
{{ __('Goods must be packaged according to marketplace requirements:') }}
                </p>
                <ul class="list-disc list-inside space-y-2 text-body-m text-text-muted">
                    <li>
{{ __('Individual packaging for each unit') }}
                    </li>
                    <li>
{{ __('Damage protection during transportation') }}
                    </li>
                    <li>
{{ __('Barcode labeling') }}
                    </li>
                </ul>
            </div>
            
            <div class="card mb-8">
                <h2 class="text-h3 font-heading mb-4">
{{ __('Responsibility') }}
                </h2>
                <p class="text-body-m text-text-muted mb-4">
{{ __('RISMENT is responsible for:') }}
                </p>
                <ul class="list-disc list-inside space-y-2 text-body-m text-text-muted">
                    <li>
{{ __('Product safety in the warehouse') }}
                    </li>
                    <li>
{{ __('Order assembly accuracy') }}
                    </li>
                    <li>
{{ __('SLA compliance timelines') }}
                    </li>
                </ul>
            </div>
            
            <div class="card gradient-brand text-white p-6 sm:p-8">
                <h3 class="text-h3 font-heading mb-4">
{{ __('EDBS Features') }}
                </h3>
                <p class="text-body-m opacity-90">
{{ __('Varies by platform. Wildberries and Uzum have different labeling and packaging requirements for EDBS scheme.') }}
                </p>
            </div>
        </div>
    </div>
</section>
@endsection
