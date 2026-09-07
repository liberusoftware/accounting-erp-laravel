<?php

declare(strict_types=1);

namespace Liberu\Accounting\ChartOfAccountsApi;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\ChartOfAccounts\Models\Account;
use Liberu\Accounting\ChartOfAccountsApi\Policies\AccountPolicy;

final class ChartOfAccountsApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(Account::class, AccountPolicy::class);
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
