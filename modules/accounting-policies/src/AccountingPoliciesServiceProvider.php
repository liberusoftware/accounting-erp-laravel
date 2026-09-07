<?php

declare(strict_types=1);

namespace Liberu\Accounting\Policies;

use Illuminate\Support\ServiceProvider;

final class AccountingPoliciesServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
