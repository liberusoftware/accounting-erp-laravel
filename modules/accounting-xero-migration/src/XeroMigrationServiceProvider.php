<?php

declare(strict_types=1);

namespace Liberu\Accounting\XeroMigration;

use Illuminate\Support\ServiceProvider;

final class XeroMigrationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
