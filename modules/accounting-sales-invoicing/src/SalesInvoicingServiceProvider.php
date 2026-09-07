<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesInvoicing;

use Illuminate\Support\ServiceProvider;

final class SalesInvoicingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
