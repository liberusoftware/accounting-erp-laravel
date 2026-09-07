<?php

declare(strict_types=1);

namespace Liberu\Accounting\MigrationFramework;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\MigrationFramework\Queries\MigrationQuery;

final class MigrationFrameworkServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    public function register(): void
    {
        $this->app->singleton(MigrationQuery::class);
    }
}
