<?php

declare(strict_types=1);

namespace Liberu\Accounting\ContractorCompliance;

use Illuminate\Support\ServiceProvider;

final class ContractorComplianceServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
