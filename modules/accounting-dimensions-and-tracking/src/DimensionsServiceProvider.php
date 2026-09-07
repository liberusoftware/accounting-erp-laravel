<?php

declare(strict_types=1);

namespace Liberu\Accounting\Dimensions;

use Illuminate\Support\ServiceProvider;

final class DimensionsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
