<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollIntegrationFilament;

use Illuminate\Support\ServiceProvider;

final class PayrollIntegrationFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PayrollIntegrationFilamentPlugin::class);
    }
}
