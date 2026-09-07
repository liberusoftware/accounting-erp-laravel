<?php

declare(strict_types=1);

namespace Liberu\Accounting\Depreciation;

use Illuminate\Support\ServiceProvider;

final class DepreciationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
