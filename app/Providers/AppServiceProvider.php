<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register TelegramService as singleton
        $this->app->singleton(\App\Services\TelegramService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register tariff audit observers
        \App\Models\PricingRate::observe(\App\Observers\PricingRateObserver::class);
        
        // Register notification observers
        \App\Models\Ticket::observe(\App\Observers\TicketObserver::class);
        \App\Models\Lead::observe(\App\Observers\LeadObserver::class);
        
        // Force HTTPS in production
        if ($this->app->environment('production')) {
            \URL::forceScheme('https');
        }
    }
}
