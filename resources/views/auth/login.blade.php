<x-guest-layout>
    <div class="space-y-6">
        <div class="text-center">
            <h2 class="text-h2 font-heading text-brand-dark mb-2">{{ __('Welcome Back') }}</h2>
            <p class="text-body-m text-brand-muted">{{ __('Log in to your account') }}</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

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
                           autofocus 
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
                           autocomplete="current-password"
                           placeholder="{{ __('••••••••') }}">
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

            <!-- Remember Me -->
            <div class="flex items-center justify-between">
                <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                    <input id="remember_me" 
                           type="checkbox" 
                           class="rounded border-brand-border text-brand focus:ring-brand focus:ring-offset-0 transition-all cursor-pointer" 
                           name="remember">
                    <span class="ml-2 text-body-s text-brand-dark group-hover:text-brand transition-colors">{{ __('Remember me') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-body-s text-brand hover:text-brand-dark font-medium transition-all hover:underline" href="{{ route('password.request') }}">
                        {{ __('Forgot password?') }}
                    </a>
                @endif
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary w-full text-body-l font-semibold py-3 transition-all duration-300 hover:shadow-lg hover:-translate-y-0.5">
                {{ __('Log in') }}
            </button>

            <!-- Register Link -->
            <div class="text-center pt-4 border-t border-brand-border/20">
                <p class="text-body-s text-brand-muted">
                    {{ __("Don't have an account?") }}
                    <a href="{{ route('register') }}" class="text-brand hover:text-brand-dark font-semibold transition-all hover:underline ml-1">
                        {{ __('Register') }}
                    </a>
                </p>
            </div>
        </form>
    </div>
</x-guest-layout>
