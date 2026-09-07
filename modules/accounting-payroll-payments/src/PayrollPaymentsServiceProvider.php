<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollPayments;

use Illuminate\Support\ServiceProvider;

final class PayrollPaymentsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
