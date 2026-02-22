<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'ensureCompany' => \App\Http\Middleware\EnsureUserHasCompany::class,
        ]);
        
        // Exclude payment gateway callbacks from CSRF verification
        $middleware->validateCsrfTokens(except: [
            'payment/click',
            'payment/payme',
        ]);
        
        // Redirect unauthenticated users based on panel
        $middleware->redirectGuestsTo(function (\Illuminate\Http\Request $request) {
            if (str_starts_with($request->path(), 'manager')) {
                return route('manager.login');
            }
            return route('login', ['locale' => 'ru']);
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
