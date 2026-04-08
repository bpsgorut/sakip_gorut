<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Providers\BreadcrumbsServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->register(BreadcrumbsServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
