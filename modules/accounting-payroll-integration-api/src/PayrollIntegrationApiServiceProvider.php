<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollIntegrationApi;

use Illuminate\Support\ServiceProvider;

final class PayrollIntegrationApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
