<?php

declare(strict_types=1);

namespace Liberu\Accounting\DebtAndLoansApi;

use Illuminate\Support\ServiceProvider;

final class DebtAndLoansApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
