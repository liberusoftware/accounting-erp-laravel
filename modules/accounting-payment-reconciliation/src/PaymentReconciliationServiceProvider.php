<?php

declare(strict_types=1);

namespace Liberu\Accounting\PaymentReconciliation;

use Illuminate\Support\ServiceProvider;

final class PaymentReconciliationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
