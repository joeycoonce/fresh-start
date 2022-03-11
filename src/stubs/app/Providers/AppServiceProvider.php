<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Overriding default backpack controllers to support users first and last names
        $this->app->bind(
            \Backpack\PermissionManager\app\Http\Controllers\UserCrudController::class,
            \App\Http\Controllers\Admin\UserCrudController::class
        );
        $this->app->bind(
            \Backpack\CRUD\app\Http\Controllers\MyAccountController::class,
            \App\Http\Controllers\Admin\MyAccountController::class
        );
        $this->app->bind(
            \Backpack\CRUD\app\Http\Controllers\Auth\RegisterController::class,
            \App\Http\Controllers\Admin\Auth\RegisterController::class
        );
    }
}
