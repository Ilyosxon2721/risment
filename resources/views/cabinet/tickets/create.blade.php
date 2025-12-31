@extends('cabinet.layout')

@section('title', __('Create Ticket'))

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-h1 font-heading">{{ __('Create Ticket') }}</h1>
            <p class="text-body-m text-text-muted mt-2">{{ __('Contact support') }}</p>
        </div>
        <a href="{{ route('cabinet.tickets.index') }}" class="btn btn-secondary">
            ← {{ __('Back') }}
        </a>
    </div>
</div>

<form action="{{ route('cabinet.tickets.store') }}" method="POST">
    @csrf
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Form -->
        <div class="lg:col-span-2">
            <div class="card">
                <h2 class="text-h3 font-heading mb-6">{{ __('Ticket Details') }}</h2>
                
                <!-- Subject -->
                <div class="mb-6">
                    <label for="subject" class="block text-body-m font-semibold text-brand-dark mb-2">
                        {{ __('Subject') }} *
                    </label>
                    <input 
                        type="text" 
                        id="subject" 
                        name="subject" 
                        value="{{ old('subject') }}" 
                        class="input w-full @error('subject') border-error @enderror"
                        placeholder="{{ __('Brief description of your issue') }}"
                        required
                    >
                    @error('subject')
                        <p class="text-error text-body-s mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Priority -->
                <div class="mb-6">
                    <label for="priority" class="block text-body-m font-semibold text-brand-dark mb-2">
                        {{ __('Priority') }} *
                    </label>
                    <select 
                        id="priority" 
                        name="priority" 
                        class="input w-full @error('priority') border-error @enderror"
                        required
                    >
                        <option value="">{{ __('Select priority') }}</option>
                        <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>{{ __('Low') }} - {{ __('General question') }}</option>
                        <option value="medium" {{ old('priority') === 'medium' ? 'selected' : '' }}>{{ __('Medium') }} - {{ __('Need assistance') }}</option>
                        <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>{{ __('High') }} - {{ __('Urgent issue') }}</option>
                    </select>
                    @error('priority')
                        <p class="text-error text-body-s mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Message -->
                <div class="mb-6">
                    <label for="message" class="block text-body-m font-semibold text-brand-dark mb-2">
                        {{ __('Message') }} *
                    </label>
                    <textarea 
                        id="message" 
                        name="message" 
                        rows="8" 
                        class="input w-full @error('message') border-error @enderror"
                        placeholder="{{ __('Describe your issue in detail...') }}"
                        required
                    >{{ old('message') }}</textarea>
                    @error('message')
                        <p class="text-error text-body-s mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Submit Button -->
                <div class="flex justify-end gap-3">
                    <a href="{{ route('cabinet.tickets.index') }}" class="btn btn-ghost">
                        {{ __('Cancel') }}
                    </a>
                    <button type="submit" class="btn btn-primary">
                        {{ __('Create Ticket') }}
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Help Sidebar -->
        <div class="lg:col-span-1">
            <div class="card sticky top-4">
                <h3 class="text-h4 font-heading mb-4">{{ __('Tips') }}</h3>
                
                <div class="space-y-4 text-body-s">
                    <div>
                        <div class="font-semibold text-brand-dark mb-1">{{ __('Be specific') }}</div>
                        <div class="text-text-muted">{{ __('Provide as much detail as possible about your issue') }}</div>
                    </div>
                    
                    <div>
                        <div class="font-semibold text-brand-dark mb-1">{{ __('Include details') }}</div>
                        <div class="text-text-muted">{{ __('Reference numbers, dates, or specific items help us assist you faster') }}</div>
                    </div>
                    
                    <div>
                        <div class="font-semibold text-brand-dark mb-1">{{ __('Response time') }}</div>
                        <div class="text-text-muted">{{ __('We typically respond within 24 hours') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
