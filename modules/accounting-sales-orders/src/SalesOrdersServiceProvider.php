<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesOrders;

use Illuminate\Support\ServiceProvider;

final class SalesOrdersServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
