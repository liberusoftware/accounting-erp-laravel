<?php

declare(strict_types=1);

namespace Liberu\Accounting\Leases;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\Leases\Queries\LeaseQuery;

final class LeasesServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    public function register(): void
    {
        $this->app->singleton(LeaseQuery::class);
    }
}
