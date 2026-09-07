<?php

declare(strict_types=1);

namespace Liberu\Accounting\YearEnd;

use Illuminate\Support\ServiceProvider;

final class YearEndServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
