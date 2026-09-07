<?php

declare(strict_types=1);

namespace Liberu\Accounting\FinancialMasterData;

use Illuminate\Support\ServiceProvider;

final class FinancialMasterDataServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
