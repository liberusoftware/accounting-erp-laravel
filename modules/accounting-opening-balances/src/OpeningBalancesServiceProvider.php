<?php

declare(strict_types=1);

namespace Liberu\Accounting\OpeningBalances;

use Illuminate\Support\ServiceProvider;

final class OpeningBalancesServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
