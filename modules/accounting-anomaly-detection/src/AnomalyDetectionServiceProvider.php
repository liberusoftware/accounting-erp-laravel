<?php

declare(strict_types=1);

namespace Liberu\Accounting\AnomalyDetection;

use Illuminate\Support\ServiceProvider;

final class AnomalyDetectionServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
