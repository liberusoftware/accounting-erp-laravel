<?php

declare(strict_types=1);

namespace Liberu\Accounting\CashFlowForecasting;

use Illuminate\Support\ServiceProvider;

final class CashFlowForecastingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
