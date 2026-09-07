<?php

declare(strict_types=1);

namespace Liberu\Accounting\TaxCore;

use Illuminate\Support\ServiceProvider;

final class TaxCoreServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
