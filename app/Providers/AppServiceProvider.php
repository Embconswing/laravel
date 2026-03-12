<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
use App\Models\Configuration;
use Illuminate\Contracts\View\Factory as ViewFactory;
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }



public function boot(): void
{
    // ✅ FORCE register paginator view factory (CRITICAL FIX)
    Paginator::viewFactoryResolver(function () {
        return app(ViewFactory::class);
    });

    // ✅ Pagination styling
    Paginator::useBootstrapFive();

    // Optional shared data (safe)
    if (! $this->app->runningInConsole()) {
        \Illuminate\Support\Facades\View::share(
            'registname',
            \App\Models\Configuration::value('Registname')
        );
    }
}

    
    }

