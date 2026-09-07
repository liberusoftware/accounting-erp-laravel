<?php

declare(strict_types=1);

namespace Liberu\Accounting\ManagementReporting;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\ManagementReporting\Queries\ReportQuery;

final class ManagementReportingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    public function register(): void
    {
        $this->app->singleton(ReportQuery::class);
    }
}
