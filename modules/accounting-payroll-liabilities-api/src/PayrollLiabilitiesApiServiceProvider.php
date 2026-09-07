<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollLiabilitiesApi;

use Illuminate\Support\ServiceProvider;

final class PayrollLiabilitiesApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
