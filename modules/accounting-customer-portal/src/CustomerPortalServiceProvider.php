<?php

declare(strict_types=1);

namespace Liberu\Accounting\CustomerPortal;

use Illuminate\Support\ServiceProvider;

final class CustomerPortalServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
