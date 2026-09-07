<?php

declare(strict_types=1);

namespace Liberu\Accounting\DepositsAndClearing;

use Illuminate\Support\ServiceProvider;

final class DepositsAndClearingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
