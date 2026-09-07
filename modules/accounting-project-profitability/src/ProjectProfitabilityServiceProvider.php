<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectProfitability;

use Illuminate\Support\ServiceProvider;

final class ProjectProfitabilityServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
