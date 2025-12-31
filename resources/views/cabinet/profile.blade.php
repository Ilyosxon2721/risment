@extends('cabinet.layout')

@section('title', __('Profile'))

@section('content')
<div class="mb-8">
    <h1 class="text-h1 font-heading">{{ __('Profile') }}</h1>
    <p class="text-body-m text-text-muted mt-2">{{ __('Manage your account settings') }}</p>
</div>

@if(session('success'))
<div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-btn mb-6">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-btn mb-6">
    {{ session('error') }}
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Profile Form -->
    <div class="lg:col-span-2">
        <div class="card">
            <h2 class="text-h3 font-heading mb-6">{{ __('Personal Information') }}</h2>
            
            <form action="{{ route('cabinet.profile.update') }}" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Name -->
                <div class="mb-6">
                    <label for="name" class="block text-body-m font-semibold text-brand-dark mb-2">
                        {{ __('Name') }}
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="{{ old('name', $user->name) }}" 
                        class="input w-full @error('name') border-error @enderror"
                        required
                    >
                    @error('name')
                        <p class="text-error text-body-s mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Email -->
                <div class="mb-6">
                    <label for="email" class="block text-body-m font-semibold text-brand-dark mb-2">
                        {{ __('Email') }}
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="{{ old('email', $user->email) }}" 
                        class="input w-full @error('email') border-error @enderror"
                        required
                    >
                    @error('email')
                        <p class="text-error text-body-s mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Phone -->
                <div class="mb-6">
                    <label for="phone" class="block text-body-m font-semibold text-brand-dark mb-2">
                        {{ __('Phone') }}
                    </label>
                    <input 
                        type="tel" 
                        id="phone" 
                        name="phone" 
                        value="{{ old('phone', $user->phone) }}" 
                        class="input w-full @error('phone') border-error @enderror"
                        placeholder="+998 XX XXX XX XX"
                    >
                    @error('phone')
                        <p class="text-error text-body-s mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit" class="btn btn-primary">
                        {{ __('Save Changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Company Info Sidebar -->
    <div class="lg:col-span-1">
        <div class="card">
            <h3 class="text-h4 font-heading mb-4">{{ __('Current Company') }}</h3>
            
            @if($currentCompany)
            <div class="p-4 bg-bg-soft rounded-btn mb-4">
                <div class="font-semibold text-brand-dark">{{ $currentCompany->name }}</div>
                @if($currentCompany->contact_name)
                <div class="text-body-s text-text-muted mt-1">{{ $currentCompany->contact_name }}</div>
                @endif
                @if($currentCompany->email)
                <div class="text-body-s text-text-muted">{{ $currentCompany->email }}</div>
                @endif
            </div>
            @endif
            
            @if($user->companies->count() > 1)
            <div class="border-t border-brand-border pt-4">
                <h4 class="text-body-m font-semibold mb-3">{{ __('Switch Company') }}</h4>
                @foreach($user->companies as $company)
                    @if($company->id !== $currentCompany->id)
                    <form action="{{ route('cabinet.profile.switch-company', ['company' => $company->id]) }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 bg-bg-soft hover:bg-brand-light rounded-btn transition text-body-s">
                            {{ $company->name }}
                        </button>
                    </form>
                    @endif
                @endforeach
            </div>
            @endif
        </div>
        
        <!-- Account Stats -->
        <div class="card mt-6">
            <h3 class="text-h4 font-heading mb-4">{{ __('Account') }}</h3>
            <div class="space-y-3 text-body-s">
                <div class="flex justify-between">
                    <span class="text-text-muted">{{ __('Member since') }}</span>
                    <span class="font-semibold">{{ $user->created_at->format('d.m.Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-text-muted">{{ __('Companies') }}</span>
                    <span class="font-semibold">{{ $user->companies->count() }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
