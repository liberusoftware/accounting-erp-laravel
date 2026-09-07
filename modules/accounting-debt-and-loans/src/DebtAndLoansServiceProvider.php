<?php

declare(strict_types=1);

namespace Liberu\Accounting\DebtAndLoans;

use Illuminate\Support\ServiceProvider;

final class DebtAndLoansServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
