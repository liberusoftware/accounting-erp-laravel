<?php

declare(strict_types=1);

namespace Liberu\Accounting\Workpapers;

use Illuminate\Support\ServiceProvider;

final class WorkpapersServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
