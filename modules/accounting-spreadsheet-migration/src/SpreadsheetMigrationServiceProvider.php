<?php

declare(strict_types=1);

namespace Liberu\Accounting\SpreadsheetMigration;

use Illuminate\Support\ServiceProvider;

final class SpreadsheetMigrationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
