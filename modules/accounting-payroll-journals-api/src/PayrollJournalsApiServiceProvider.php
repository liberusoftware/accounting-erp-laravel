<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollJournalsApi;

use Illuminate\Support\ServiceProvider;

final class PayrollJournalsApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
