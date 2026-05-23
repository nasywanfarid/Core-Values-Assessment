<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

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
        Paginator::useBootstrapFive();

        if (str_contains(config('app.url'), 'https') && !in_array(request()->getHost(), ['127.0.0.1', 'localhost'])) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}
