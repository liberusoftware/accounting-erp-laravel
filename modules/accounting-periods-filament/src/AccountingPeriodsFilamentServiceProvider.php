<?php

namespace Liberu\Accounting\PeriodsFilament;

use Illuminate\Support\ServiceProvider;

final class AccountingPeriodsFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AccountingPeriodsFilamentPlugin::class);
    }
}
