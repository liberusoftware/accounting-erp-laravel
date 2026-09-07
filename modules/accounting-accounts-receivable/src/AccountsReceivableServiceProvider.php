<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsReceivable;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\AccountsReceivable\Listeners\SyncFinalizedInvoiceListener;
use Liberu\Accounting\SalesInvoicing\Events\InvoiceFinalized;

class AccountsReceivableServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    public function register(): void
    {
        $this->app['events']->listen(InvoiceFinalized::class, SyncFinalizedInvoiceListener::class);
    }
}
