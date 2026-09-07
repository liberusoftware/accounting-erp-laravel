<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankReconciliation;

use Illuminate\Support\ServiceProvider;

final class BankReconciliationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
