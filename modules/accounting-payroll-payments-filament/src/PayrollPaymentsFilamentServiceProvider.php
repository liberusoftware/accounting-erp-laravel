<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollPaymentsFilament;

use Illuminate\Support\ServiceProvider;

final class PayrollPaymentsFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PayrollPaymentsFilamentPlugin::class);
    }
}
