<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        // Fetch necessary data
       
        
        // Use View::composer to pass data to all views
        View::composer('*', function ($view) {
            $view->with([
                
            ]);
        });
    }
}
