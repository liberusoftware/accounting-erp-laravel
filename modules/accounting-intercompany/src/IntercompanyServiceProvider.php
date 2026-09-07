<?php

declare(strict_types=1);

namespace Liberu\Accounting\Intercompany;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\Intercompany\Queries\IntercompanyQuery;

final class IntercompanyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(IntercompanyQuery::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
