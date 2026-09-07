<?php

declare(strict_types=1);

namespace Liberu\Accounting\AssetEvents;

use Illuminate\Support\ServiceProvider;

final class AssetEventsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
