<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankRules;

use Illuminate\Support\ServiceProvider;

final class BankRulesServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
