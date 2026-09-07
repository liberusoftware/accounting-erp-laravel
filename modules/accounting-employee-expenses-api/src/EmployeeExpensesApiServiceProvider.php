<?php

declare(strict_types=1);

namespace Liberu\Accounting\EmployeeExpensesApi;

use Illuminate\Support\ServiceProvider;

final class EmployeeExpensesApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
