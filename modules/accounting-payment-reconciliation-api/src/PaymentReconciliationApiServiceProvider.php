<?php

declare(strict_types=1);

namespace Liberu\Accounting\PaymentReconciliationApi;

use Illuminate\Support\ServiceProvider;

final class PaymentReconciliationApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
