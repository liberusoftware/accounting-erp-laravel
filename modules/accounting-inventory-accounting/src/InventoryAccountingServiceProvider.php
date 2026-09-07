<?php

declare(strict_types=1);

namespace Liberu\Accounting\InventoryAccounting;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\InventoryAccounting\Queries\InventoryQuery;

final class InventoryAccountingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(InventoryQuery::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
