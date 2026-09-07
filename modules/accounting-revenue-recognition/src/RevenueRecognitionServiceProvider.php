<?php

declare(strict_types=1);

namespace Liberu\Accounting\RevenueRecognition;

use Illuminate\Support\ServiceProvider;

final class RevenueRecognitionServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
