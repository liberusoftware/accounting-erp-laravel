<?php

declare(strict_types=1);

namespace Liberu\Accounting\MigrationFrameworkApi;

use Illuminate\Support\ServiceProvider;

final class MigrationFrameworkApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
