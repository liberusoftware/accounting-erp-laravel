<?php

declare(strict_types=1);

namespace Liberu\Accounting\MultiEntity;

use Illuminate\Support\ServiceProvider;

final class MultiEntityServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
