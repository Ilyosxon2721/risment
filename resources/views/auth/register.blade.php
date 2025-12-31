<x-guest-layout>
    <div class="space-y-6">
        <div class="text-center">
            <h2 class="text-h2 font-heading text-brand-dark mb-2">{{ __('Create Account') }}</h2>
            <p class="text-body-m text-brand-muted">{{ __('Join us today') }}</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            <!-- Name -->
            <div class="group">
                <label for="name" class="block text-body-m font-semibold text-brand-dark mb-2">
                    {{ __('Name') }}
                </label>
                <div class="relative">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-brand-muted group-focus-within:text-brand transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <input id="name" 
                           class="input w-full pl-12 transition-all duration-300 focus:ring-2 focus:ring-brand focus:border-brand" 
                           type="text" 
                           name="name" 
                           value="{{ old('name') }}" 
                           required 
                           autofocus 
                           autocomplete="name"
                           placeholder="{{ __('Your full name') }}">
                </div>
                @error('name')
                    <p class="text-error text-body-s mt-2 flex items-center gap-1 animate-fade-in">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Email Address -->
            <div class="group">
                <label for="email" class="block text-body-m font-semibold text-brand-dark mb-2">
                    {{ __('Email') }}
                </label>
                <div class="relative">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-brand-muted group-focus-within:text-brand transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                    </svg>
                    <input id="email" 
                           class="input w-full pl-12 transition-all duration-300 focus:ring-2 focus:ring-brand focus:border-brand" 
                           type="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           autocomplete="username"
                           placeholder="{{ __('your@email.com') }}">
                </div>
                @error('email')
                    <p class="text-error text-body-s mt-2 flex items-center gap-1 animate-fade-in">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Password -->
            <div class="group">
                <label for="password" class="block text-body-m font-semibold text-brand-dark mb-2">
                    {{ __('Password') }}
                </label>
                <div class="relative">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-brand-muted group-focus-within:text-brand transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <input id="password" 
                           class="input w-full pl-12 transition-all duration-300 focus:ring-2 focus:ring-brand focus:border-brand" 
                           type="password" 
                           name="password" 
                           required 
                           autocomplete="new-password"
                           placeholder="••••••••">
                </div>
                @error('password')
                    <p class="text-error text-body-s mt-2 flex items-center gap-1 animate-fade-in">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="group">
                <label for="password_confirmation" class="block text-body-m font-semibold text-brand-dark mb-2">
                    {{ __('Confirm Password') }}
                </label>
                <div class="relative">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-brand-muted group-focus-within:text-brand transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <input id="password_confirmation" 
                           class="input w-full pl-12 transition-all duration-300 focus:ring-2 focus:ring-brand focus:border-brand" 
                           type="password" 
                           name="password_confirmation" 
                           required 
                           autocomplete="new-password"
                           placeholder="••••••••">
                </div>
                @error('password_confirmation')
                    <p class="text-error text-body-s mt-2 flex items-center gap-1 animate-fade-in">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary w-full text-body-l font-semibold py-3 transition-all duration-300 hover:shadow-lg hover:-translate-y-0.5">
                {{ __('Create Account') }}
            </button>

            <!-- Login Link -->
            <div class="text-center pt-4 border-t border-brand-border/20">
                <p class="text-body-s text-brand-muted">
                    {{ __('Already have an account?') }}
                    <a href="{{ route('login') }}" class="text-brand hover:text-brand-dark font-semibold transition-all hover:underline ml-1">
                        {{ __('Log in') }}
                    </a>
                </p>
            </div>
        </form>
    </div>
</x-guest-layout>
