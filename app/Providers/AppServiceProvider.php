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
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Load custom helpers
        require_once app_path('helpers.php');
        
        // Set default pagination view to our custom template
        \Illuminate\Pagination\Paginator::defaultView('vendor.pagination.custom');
        \Illuminate\Pagination\Paginator::defaultSimpleView('vendor.pagination.custom');
    }
}
