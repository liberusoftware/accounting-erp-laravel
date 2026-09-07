<?php

declare(strict_types=1);

namespace Liberu\Accounting\Budgets;

use Illuminate\Support\ServiceProvider;

final class BudgetsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
