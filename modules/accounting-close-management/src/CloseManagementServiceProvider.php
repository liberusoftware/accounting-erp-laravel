<?php

declare(strict_types=1);

namespace Liberu\Accounting\CloseManagement;

use Illuminate\Support\ServiceProvider;

final class CloseManagementServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
