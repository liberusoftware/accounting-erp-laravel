<?php

declare(strict_types=1);

namespace Liberu\Accounting\ReceiptManagement;

use Illuminate\Support\ServiceProvider;

final class ReceiptManagementServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
