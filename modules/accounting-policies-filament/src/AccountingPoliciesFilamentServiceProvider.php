<?php

namespace Liberu\Accounting\PoliciesFilament;

use Illuminate\Support\ServiceProvider;

final class AccountingPoliciesFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AccountingPoliciesFilamentPlugin::class);
    }
}
