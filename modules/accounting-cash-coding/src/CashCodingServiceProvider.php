<?php

declare(strict_types=1);

namespace Liberu\Accounting\CashCoding;

use Illuminate\Support\ServiceProvider;

final class CashCodingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
