<?php

declare(strict_types=1);

namespace Liberu\Accounting\MultiCurrency;

use Illuminate\Support\ServiceProvider;

final class MultiCurrencyServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
