<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollIntegration;

use Illuminate\Support\ServiceProvider;

final class PayrollIntegrationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
