<?php

declare(strict_types=1);

namespace Liberu\Accounting\Periods;

use Illuminate\Support\ServiceProvider;

final class AccountingPeriodsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
