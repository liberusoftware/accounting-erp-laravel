<?php

declare(strict_types=1);

namespace Liberu\Accounting\KpiAndGoals;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\KpiAndGoals\Queries\KpiQuery;

final class KpiAndGoalsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    public function register(): void
    {
        $this->app->singleton(KpiQuery::class);
    }
}
