<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectBilling;

use Illuminate\Support\ServiceProvider;

final class ProjectBillingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
