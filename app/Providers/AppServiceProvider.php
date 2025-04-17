<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Auth\AuthService;
Use App\Contracts\AuthInterface;
use App\Interfaces\UserInterface;
use App\Services\UserService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $services = [
            AuthInterface::class => AuthService::class,
            UserInterface::class => UserService::class
        ];

        foreach ($services as $interface => $implementation) {
            $this->app->bind($interface, $implementation);
        }
        $this->loadHelpers();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }


    private function loadHelpers()
    {
        $helperFile = app_path('Helper/Helpers.php');

        if (file_exists($helperFile)) {
            require_once $helperFile;
        }
    }
}
