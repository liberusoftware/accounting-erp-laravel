<?php

declare(strict_types=1);

namespace Liberu\Accounting\EInvoicing;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\EInvoicing\Queries\EInvoiceQuery;

final class EInvoicingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(EInvoiceQuery::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
