<?php

declare(strict_types=1);

namespace Liberu\Accounting\PurchaseOrdersApi;

use Illuminate\Support\ServiceProvider;

final class PurchaseOrdersApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
