<?php

declare(strict_types=1);

namespace Liberu\Accounting\SageAccountingMigration;

use Illuminate\Support\ServiceProvider;

final class SageAccountingMigrationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
