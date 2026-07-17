<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    \App\Models\User::where('is_premium', true)
        ->whereNotNull('premium_expires_at')
        ->where('premium_expires_at', '<=', now())
        ->update([
            'is_premium' => false,
        ]);

    \App\Models\Subscription::where('status', 'active')
        ->whereNotNull('ends_at')
        ->where('ends_at', '<=', now())
        ->update([
            'status' => 'expired',
        ]);
})->daily();
