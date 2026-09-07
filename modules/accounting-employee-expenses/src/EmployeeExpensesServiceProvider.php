<?php

declare(strict_types=1);

namespace Liberu\Accounting\EmployeeExpenses;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\EmployeeExpenses\Queries\ExpenseClaimQuery;

final class EmployeeExpensesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ExpenseClaimQuery::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
