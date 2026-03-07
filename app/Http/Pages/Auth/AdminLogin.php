<?php

namespace App\Http\Pages\Auth;

use Filament\Pages\Auth\Login;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;

class AdminLogin extends Login
{
    public function authenticate(): ?LoginResponse
    {
        // Call parent but override session regeneration behavior
        // to avoid invalidating other guard sessions (manager/web)
        try {
            $this->rateLimit(5);
        } catch (\Filament\Exceptions\TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();
            return null;
        }

        $data = $this->form->getState();

        if (!\Filament\Facades\Filament::auth()->attempt($this->getCredentialsFromFormData($data), $data['remember'] ?? false)) {
            $this->throwFailureValidationException();
        }

        $user = \Filament\Facades\Filament::auth()->user();

        if (
            ($user instanceof \Filament\Models\Contracts\FilamentUser) &&
            (! $user->canAccessPanel(\Filament\Facades\Filament::getCurrentPanel()))
        ) {
            \Filament\Facades\Filament::auth()->logout();
            $this->throwFailureValidationException();
        }

        // Use regenerateToken() instead of regenerate() to avoid
        // destroying other guards' sessions stored in the same session
        session()->regenerateToken();

        return app(LoginResponse::class);
    }
}
