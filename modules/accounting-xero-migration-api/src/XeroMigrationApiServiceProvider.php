<?php

declare(strict_types=1);

namespace Liberu\Accounting\XeroMigrationApi;

use Illuminate\Support\ServiceProvider;

final class XeroMigrationApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
