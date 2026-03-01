<?php

namespace App\Http\Controllers\Filament;

use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse;

class LogoutController
{
    public function __invoke(): LogoutResponse
    {
        Filament::auth()->logout();

        // Do NOT call session()->invalidate() — it would destroy all guards' sessions
        // (web, admin, manager). Just regenerate the CSRF token.
        session()->regenerateToken();

        return app(LogoutResponse::class);
    }
}
