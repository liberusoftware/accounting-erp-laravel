<?php

declare(strict_types=1);

namespace Liberu\Accounting\KpiAndGoalsApi;

use Illuminate\Support\ServiceProvider;

final class KpiAndGoalsApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
