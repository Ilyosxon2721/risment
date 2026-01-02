@extends('cabinet.layout')

@section('title', __('Create Company'))

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-8">
        <h1 class="text-h1 font-heading">{{ __('Create Your Company') }}</h1>
        <p class="text-body-m text-text-muted mt-2">{{ __('To access the cabinet, you need to create or join a company.') }}</p>
    </div>

    <div class="card">
        <form action="{{ route('cabinet.company.store') }}" method="POST">
            @csrf

            <!-- Company Name -->
            <div class="mb-6">
                <label for="name" class="block text-body-m font-semibold mb-2">{{ __('Company Name') }} <span class="text-error">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    class="w-full px-4 py-3 border border-brand-border rounded-btn focus:ring-2 focus:ring-brand focus:border-brand transition @error('name') border-error @enderror"
                    placeholder="{{ __('Enter company name') }}">
                @error('name')
                    <p class="text-error text-body-s mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- INN -->
            <div class="mb-6">
                <label for="inn" class="block text-body-m font-semibold mb-2">{{ __('INN (Tax ID)') }} <span class="text-error">*</span></label>
                <input type="text" name="inn" id="inn" value="{{ old('inn') }}" required
                    class="w-full px-4 py-3 border border-brand-border rounded-btn focus:ring-2 focus:ring-brand focus:border-brand transition @error('inn') border-error @enderror"
                    placeholder="{{ __('Enter tax identification number') }}">
                @error('inn')
                    <p class="text-error text-body-s mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Contact Name -->
            <div class="mb-6">
                <label for="contact_name" class="block text-body-m font-semibold mb-2">{{ __('Contact Person') }}</label>
                <input type="text" name="contact_name" id="contact_name" value="{{ old('contact_name') }}"
                    class="w-full px-4 py-3 border border-brand-border rounded-btn focus:ring-2 focus:ring-brand focus:border-brand transition @error('contact_name') border-error @enderror"
                    placeholder="{{ __('Enter contact person name') }}">
                @error('contact_name')
                    <p class="text-error text-body-s mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Phone -->
            <div class="mb-6">
                <label for="phone" class="block text-body-m font-semibold mb-2">{{ __('Phone') }}</label>
                <input type="tel" name="phone" id="phone" value="{{ old('phone') }}"
                    class="w-full px-4 py-3 border border-brand-border rounded-btn focus:ring-2 focus:ring-brand focus:border-brand transition @error('phone') border-error @enderror"
                    placeholder="+998 XX XXX XX XX">
                @error('phone')
                    <p class="text-error text-body-s mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div class="mb-6">
                <label for="email" class="block text-body-m font-semibold mb-2">{{ __('Email') }}</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}"
                    class="w-full px-4 py-3 border border-brand-border rounded-btn focus:ring-2 focus:ring-brand focus:border-brand transition @error('email') border-error @enderror"
                    placeholder="{{ __('company@example.com') }}">
                @error('email')
                    <p class="text-error text-body-s mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Address -->
            <div class="mb-8">
                <label for="address" class="block text-body-m font-semibold mb-2">{{ __('Address') }}</label>
                <textarea name="address" id="address" rows="3"
                    class="w-full px-4 py-3 border border-brand-border rounded-btn focus:ring-2 focus:ring-brand focus:border-brand transition @error('address') border-error @enderror"
                    placeholder="{{ __('Enter company address') }}">{{ old('address') }}</textarea>
                @error('address')
                    <p class="text-error text-body-s mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit -->
            <button type="submit" class="btn btn-primary w-full">
                {{ __('Create Company') }}
            </button>
        </form>
    </div>
</div>
@endsection
