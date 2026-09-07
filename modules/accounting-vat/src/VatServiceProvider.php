<?php

declare(strict_types=1);

namespace Liberu\Accounting\Vat;

use Illuminate\Support\ServiceProvider;

final class VatServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
