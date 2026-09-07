<?php

declare(strict_types=1);

namespace Liberu\Accounting\CustomerPaymentsApi;

use Illuminate\Support\ServiceProvider;

final class CustomerPaymentsApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
