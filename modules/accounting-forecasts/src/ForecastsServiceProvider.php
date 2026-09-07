<?php

declare(strict_types=1);

namespace Liberu\Accounting\Forecasts;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\Forecasts\Queries\ForecastQuery;

final class ForecastsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ForecastQuery::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
