<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsPayableApi;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\AccountsPayable\Models\PayableDispute;
use Liberu\Accounting\AccountsPayable\Models\PayableOpenItem;
use Liberu\Accounting\AccountsPayable\Models\PayablePayment;
use Liberu\Accounting\AccountsPayableApi\Policies\AccountingAccountsPayablePolicy;

final class AccountsPayableApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(PayableOpenItem::class, AccountingAccountsPayablePolicy::class);
        Gate::policy(PayablePayment::class, AccountingAccountsPayablePolicy::class);
        Gate::policy(PayableDispute::class, AccountingAccountsPayablePolicy::class);
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
