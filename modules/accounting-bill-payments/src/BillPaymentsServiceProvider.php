<?php

declare(strict_types=1);

namespace Liberu\Accounting\BillPayments;

use Illuminate\Support\ServiceProvider;

final class BillPaymentsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
