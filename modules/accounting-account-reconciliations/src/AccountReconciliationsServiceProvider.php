<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountReconciliations;

use Illuminate\Support\ServiceProvider;

final class AccountReconciliationsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
