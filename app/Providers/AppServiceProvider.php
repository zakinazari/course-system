<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Actions\Auth\LoginResponse as CustomLoginResponse;
use App\Model\Hr\PermanentContract;
use App\Model\Hr\TemporaryContract;
use Illuminate\Database\Eloquent\Relations\Relation;

use App\Models\Warehouse\BookInventory;
use App\Observers\BookInventoryObserver;
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


        Relation::morphMap([

            'permanent' => \App\Models\Hr\PermanentContract::class,
            'temporary' => \App\Models\Hr\TemporaryContract::class,

            'permanent_payroll' => \App\Models\Hr\PermanentPayroll::class,
            'temporary_payroll' => \App\Models\Hr\TemporaryPayroll::class,
        ]);

        //observes 
        BookInventory::observe(
            BookInventoryObserver::class
        );
    }
}
