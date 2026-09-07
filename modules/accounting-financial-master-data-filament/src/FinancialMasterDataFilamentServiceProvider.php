<?php

declare(strict_types=1);

namespace Liberu\Accounting\FinancialMasterDataFilament;

use Illuminate\Support\ServiceProvider;

final class FinancialMasterDataFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(FinancialMasterDataFilamentPlugin::class);
    }
}
