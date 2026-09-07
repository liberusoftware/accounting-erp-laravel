<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankAccountsApi;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\BankAccounts\Models\BankAccount;
use Liberu\Accounting\BankAccountsApi\Policies\BankAccountsPolicy;

final class BankAccountsApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(BankAccount::class, BankAccountsPolicy::class);
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
