<?php

declare(strict_types=1);

namespace Liberu\Accounting\EstimatesAndQuotes;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\EstimatesAndQuotes\Queries\EstimateQuery;

final class EstimatesAndQuotesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(EstimateQuery::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
