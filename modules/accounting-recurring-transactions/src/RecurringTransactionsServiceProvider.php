<?php

declare(strict_types=1);

namespace Liberu\Accounting\RecurringTransactions;

use Illuminate\Support\ServiceProvider;

final class RecurringTransactionsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
