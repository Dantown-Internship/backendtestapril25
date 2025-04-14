<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Dedoc\Scramble\Support\Generator\Parameter;
use Dedoc\Scramble\Support\Generator\Operation;
use Dedoc\Scramble\Scramble;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Scramble::ignoreDefaultRoutes();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if(!defined('RESULT_COUNT')) define('RESULT_COUNT', 20);

        Scramble::configure()
        ->withOperationTransformers(function (Operation $operation) {
            $operation->addParameters([
                new Parameter('X-COMPANY-ID', 'header'),
            ]);
        });
    }
}
