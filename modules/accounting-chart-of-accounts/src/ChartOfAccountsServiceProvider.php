<?php

declare(strict_types=1);

namespace Liberu\Accounting\ChartOfAccounts;

use Illuminate\Support\ServiceProvider;

final class ChartOfAccountsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
