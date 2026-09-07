<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsPayable;

use Illuminate\Support\ServiceProvider;

final class AccountsPayableServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
