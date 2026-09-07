<?php

declare(strict_types=1);

namespace Liberu\Accounting\SupplierPortal;

use Illuminate\Support\ServiceProvider;

final class SupplierPortalServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
