<?php

declare(strict_types=1);

namespace Liberu\Accounting\Consolidation;

use Illuminate\Support\ServiceProvider;

final class ConsolidationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
