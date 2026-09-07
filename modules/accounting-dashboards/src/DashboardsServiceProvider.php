<?php

declare(strict_types=1);

namespace Liberu\Accounting\Dashboards;

use Illuminate\Support\ServiceProvider;

final class DashboardsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
