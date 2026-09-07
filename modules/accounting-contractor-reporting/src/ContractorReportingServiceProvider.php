<?php

declare(strict_types=1);

namespace Liberu\Accounting\ContractorReporting;

use Illuminate\Support\ServiceProvider;

final class ContractorReportingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
