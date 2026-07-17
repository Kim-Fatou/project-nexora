<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Laravel\Fortify\Fortify;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->singleton(
            \Laravel\Fortify\Contracts\RegisterResponse::class,
            \App\Http\Responses\RegisterResponse::class
        );
    }

    /**
     * Bootstrap any application services.
     */

public function boot(): void
{
    // Indiquer à Fortify de charger tes vues
    Fortify::loginView(function () {
        return view('auth.login');
    });

    Fortify::registerView(function () {
        return view('auth.register');
    });

    Fortify::verifyEmailView(function () {
        return view('auth.verify-email');
    });

    \App\Models\ChatMessage::observe(\App\Observers\ChatMessageObserver::class);
    \App\Models\RoomMessage::observe(\App\Observers\RoomMessageObserver::class);
}
}
