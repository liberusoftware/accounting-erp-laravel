<?php

declare(strict_types=1);

namespace Liberu\Accounting\SpreadsheetMigrationApi;

use Illuminate\Support\ServiceProvider;

final class SpreadsheetMigrationApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
