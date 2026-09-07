<?php

declare(strict_types=1);

namespace Liberu\Accounting\PurchaseRequisitions;

use Illuminate\Support\ServiceProvider;

final class PurchaseRequisitionsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
