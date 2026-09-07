<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesOrdersApi;

use Illuminate\Support\ServiceProvider;

final class SalesOrdersApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
