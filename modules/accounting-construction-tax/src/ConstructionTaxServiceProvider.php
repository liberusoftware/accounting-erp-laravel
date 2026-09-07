<?php

declare(strict_types=1);

namespace Liberu\Accounting\ConstructionTax;

use Illuminate\Support\ServiceProvider;

final class ConstructionTaxServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
