<?php

declare(strict_types=1);

namespace Liberu\Accounting\CashPositionApi;

use Illuminate\Support\ServiceProvider;

final class CashPositionApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
