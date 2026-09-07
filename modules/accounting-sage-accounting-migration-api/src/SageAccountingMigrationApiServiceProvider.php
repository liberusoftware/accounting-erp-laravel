<?php

declare(strict_types=1);

namespace Liberu\Accounting\SageAccountingMigrationApi;

use Illuminate\Support\ServiceProvider;

final class SageAccountingMigrationApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
