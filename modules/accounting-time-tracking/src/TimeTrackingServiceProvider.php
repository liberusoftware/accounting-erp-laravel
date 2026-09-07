<?php

declare(strict_types=1);

namespace Liberu\Accounting\TimeTracking;

use Illuminate\Support\ServiceProvider;

final class TimeTrackingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
