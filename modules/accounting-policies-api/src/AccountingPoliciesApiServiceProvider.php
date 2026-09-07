<?php

namespace Liberu\Accounting\PoliciesApi;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\Policies\Models\PolicyRule;
use Liberu\Accounting\PoliciesApi\Policies\AccountingPoliciesPolicy;

final class AccountingPoliciesApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(PolicyRule::class, AccountingPoliciesPolicy::class);
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
