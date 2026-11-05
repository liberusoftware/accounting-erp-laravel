<?php

namespace Liberu\Accounting\ErpHttp;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

final class ErpHttpServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'accounting-erp-http');

        if (! $this->app->routesAreCached()) {
            Route::middleware('web')->group(__DIR__.'/../routes/web.php');
            Route::middleware('api')->prefix('api')->group(__DIR__.'/../routes/api.php');
        }
    }
}
