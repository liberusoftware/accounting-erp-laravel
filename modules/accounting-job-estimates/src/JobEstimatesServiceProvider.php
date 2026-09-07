<?php

declare(strict_types=1);

namespace Liberu\Accounting\JobEstimates;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\JobEstimates\Queries\JobEstimateQuery;

final class JobEstimatesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(JobEstimateQuery::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
