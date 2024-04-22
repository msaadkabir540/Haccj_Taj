<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Validator::extend('employeecode', function ($attribute, $value, $parameters, $validator) {
            // Implement your validation logic here
            // You can check if the employeecode exists in your database, for example
            // Return true if validation passes, false otherwise
            return true; // Placeholder return value, replace with your validation logic
        });
    }
}
