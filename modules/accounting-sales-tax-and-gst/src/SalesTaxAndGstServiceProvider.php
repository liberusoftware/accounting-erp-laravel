<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesTaxAndGst;

use Illuminate\Support\ServiceProvider;

final class SalesTaxAndGstServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
