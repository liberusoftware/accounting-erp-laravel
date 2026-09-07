<?php

declare(strict_types=1);

namespace Liberu\Accounting\PurchaseRequisitionsApi;

use Illuminate\Support\ServiceProvider;

final class PurchaseRequisitionsApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
