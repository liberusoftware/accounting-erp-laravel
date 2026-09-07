<?php

declare(strict_types=1);

namespace Liberu\Accounting\ThreeWayMatching;

use Illuminate\Support\ServiceProvider;

final class ThreeWayMatchingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
