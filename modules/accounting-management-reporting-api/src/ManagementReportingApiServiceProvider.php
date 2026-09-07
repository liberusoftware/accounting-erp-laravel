<?php

declare(strict_types=1);

namespace Liberu\Accounting\ManagementReportingApi;

use Illuminate\Support\ServiceProvider;

final class ManagementReportingApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
