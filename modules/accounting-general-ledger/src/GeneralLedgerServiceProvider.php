<?php

declare(strict_types=1);

namespace Liberu\Accounting\GeneralLedger;

use Illuminate\Support\ServiceProvider;

final class GeneralLedgerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
