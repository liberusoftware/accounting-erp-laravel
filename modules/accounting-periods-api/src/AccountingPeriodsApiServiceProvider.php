<?php

declare(strict_types=1);

namespace Liberu\Accounting\PeriodsApi;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\Periods\Models\AccountingPeriod;
use Liberu\Accounting\PeriodsApi\Policies\AccountingPeriodsPolicy;

final class AccountingPeriodsApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(AccountingPeriod::class, AccountingPeriodsPolicy::class);
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
