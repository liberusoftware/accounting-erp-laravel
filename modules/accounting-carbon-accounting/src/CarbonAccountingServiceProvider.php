<?php

declare(strict_types=1);

namespace Liberu\Accounting\CarbonAccounting;

use Illuminate\Support\ServiceProvider;

final class CarbonAccountingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
