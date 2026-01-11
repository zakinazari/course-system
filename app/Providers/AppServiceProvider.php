<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Actions\Auth\LoginResponse as CustomLoginResponse;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
         $this->app->bind(LoginResponse::class, CustomLoginResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        config(['app.timezone' => 'Asia/Kabul']);
        date_default_timezone_set(config('app.timezone'));
        \Carbon\Carbon::setLocale(config('app.locale'));
    }
}
