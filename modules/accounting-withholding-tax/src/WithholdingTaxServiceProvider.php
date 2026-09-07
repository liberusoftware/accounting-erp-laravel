<?php

declare(strict_types=1);

namespace Liberu\Accounting\WithholdingTax;

use Illuminate\Support\ServiceProvider;

final class WithholdingTaxServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
