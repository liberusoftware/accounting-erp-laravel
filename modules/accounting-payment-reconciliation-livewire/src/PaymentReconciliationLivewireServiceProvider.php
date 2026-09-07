<?php

declare(strict_types=1);

namespace Liberu\Accounting\PaymentReconciliationLivewire;

use Illuminate\Support\ServiceProvider;

final class PaymentReconciliationLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-accounting-payment-reconciliation');
    }
}
