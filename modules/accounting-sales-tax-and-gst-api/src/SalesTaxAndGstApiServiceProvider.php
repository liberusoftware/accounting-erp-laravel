<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesTaxAndGstApi;

use Illuminate\Support\ServiceProvider;

final class SalesTaxAndGstApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
