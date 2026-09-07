<?php

declare(strict_types=1);

namespace Liberu\Accounting\Transfers;

use Illuminate\Support\ServiceProvider;

final class TransfersServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
