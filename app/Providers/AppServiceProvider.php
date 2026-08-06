<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
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
        // Le thème du back-office est basé sur Bootstrap 5 : sans cela, la
        // pagination sortirait avec le balisage Tailwind par défaut.
        Paginator::useBootstrapFive();
    }
}
