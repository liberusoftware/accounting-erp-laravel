<?php

declare(strict_types=1);

namespace Liberu\Accounting\InventoryAccountingApi;

use Illuminate\Support\ServiceProvider;

final class InventoryAccountingApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
