<?php

declare(strict_types=1);

namespace Liberu\Accounting\WorkforceCostingApi;

use Illuminate\Support\ServiceProvider;

final class WorkforceCostingApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
