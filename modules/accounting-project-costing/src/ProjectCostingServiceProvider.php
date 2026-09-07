<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectCosting;

use Illuminate\Support\ServiceProvider;

final class ProjectCostingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
