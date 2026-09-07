<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollLiabilities;

use Illuminate\Support\ServiceProvider;

final class PayrollLiabilitiesServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
