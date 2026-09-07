<?php

declare(strict_types=1);

namespace Liberu\Accounting\RecurringTransactionsApi;

use Illuminate\Support\ServiceProvider;

final class RecurringTransactionsApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
