@extends('cabinet.layout')

@section('title', __('Company'))

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-h1 font-heading">{{ __('Company') }}</h1>
            <p class="text-body-m text-text-muted mt-2">{{ __('View and manage your company details') }}</p>
        </div>
        <a href="{{ route('cabinet.company.edit') }}" class="btn btn-primary">
            {{ __('Edit Company') }}
        </a>
    </div>
</div>

@if(session('success'))
<div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-btn mb-6">
    {{ session('success') }}
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Company Details -->
    <div class="lg:col-span-2">
        <div class="card">
            <h2 class="text-h3 font-heading mb-6">{{ __('Company Information') }}</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="text-body-s text-text-muted mb-1">{{ __('Company Name') }}</div>
                    <div class="text-body-m font-semibold">{{ $company->name }}</div>
                </div>
                
                <div>
                    <div class="text-body-s text-text-muted mb-1">{{ __('INN (Tax ID)') }}</div>
                    <div class="text-body-m font-semibold">{{ $company->inn ?? '—' }}</div>
                </div>
                
                <div>
                    <div class="text-body-s text-text-muted mb-1">{{ __('Contact Person') }}</div>
                    <div class="text-body-m font-semibold">{{ $company->contact_name ?? '—' }}</div>
                </div>
                
                <div>
                    <div class="text-body-s text-text-muted mb-1">{{ __('Phone') }}</div>
                    <div class="text-body-m font-semibold">{{ $company->phone ?? '—' }}</div>
                </div>
                
                <div>
                    <div class="text-body-s text-text-muted mb-1">{{ __('Email') }}</div>
                    <div class="text-body-m font-semibold">{{ $company->email ?? '—' }}</div>
                </div>
                
                <div>
                    <div class="text-body-s text-text-muted mb-1">{{ __('Status') }}</div>
                    <span class="badge badge-{{ $company->status === 'active' ? 'success' : 'warning' }}">
                        {{ __(ucfirst($company->status)) }}
                    </span>
                </div>
            </div>
            
            @if($company->address)
            <div class="mt-6 pt-6 border-t border-brand-border">
                <div class="text-body-s text-text-muted mb-1">{{ __('Address') }}</div>
                <div class="text-body-m">{{ $company->address }}</div>
            </div>
            @endif
        </div>
    </div>
    
    <!-- Sidebar -->
    <div class="lg:col-span-1">
        <!-- Subscription Info -->
        <div class="card">
            <h3 class="text-h4 font-heading mb-4">{{ __('Subscription') }}</h3>
            
            @if($company->subscriptionPlan)
            <div class="p-4 bg-bg-soft rounded-btn mb-4">
                <div class="font-semibold text-brand">{{ $company->subscriptionPlan->getName() }}</div>
                <div class="text-body-s text-text-muted mt-1">
                    {{ number_format($company->subscriptionPlan->price_month, 0, '', ' ') }} {{ __('UZS/month') }}
                </div>
            </div>
            <a href="{{ route('cabinet.subscription.choose') }}" class="text-brand hover:underline text-body-s">
                {{ __('Change subscription') }} →
            </a>
            @else
            <p class="text-text-muted text-body-s mb-4">{{ __('No active subscription') }}</p>
            <a href="{{ route('cabinet.subscription.choose') }}" class="btn btn-primary w-full">
                {{ __('Choose a plan') }}
            </a>
            @endif
        </div>
        
        <!-- Account Stats -->
        <div class="card mt-6">
            <h3 class="text-h4 font-heading mb-4">{{ __('Account') }}</h3>
            <div class="space-y-3 text-body-s">
                <div class="flex justify-between">
                    <span class="text-text-muted">{{ __('Balance') }}</span>
                    <span class="font-semibold {{ $company->balance >= 0 ? 'text-success' : 'text-error' }}">
                        {{ $company->formatted_balance }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-text-muted">{{ __('SKUs') }}</span>
                    <span class="font-semibold">{{ $company->skus()->count() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-text-muted">{{ __('Created') }}</span>
                    <span class="font-semibold">{{ $company->created_at->format('d.m.Y') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
