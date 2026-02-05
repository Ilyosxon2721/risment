<?php

use App\Jobs\DailyStorageChargeJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Daily storage charge accrual (runs at 2:00 AM)
Schedule::job(new DailyStorageChargeJob())
    ->dailyAt('02:00')
    ->withoutOverlapping()
    ->runInBackground();

// Monthly invoice generation (runs on 1st of each month at 6:00 AM)
Schedule::command('billing:generate-invoices --use-ledger')
    ->monthlyOn(1, '06:00')
    ->withoutOverlapping()
    ->runInBackground();
