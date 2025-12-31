<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // Don't redirect API requests
        if ($request->expectsJson()) {
            return null;
        }
        
        // Redirect to localized login page
        return route('login', ['locale' => 'ru']);
    }
}
