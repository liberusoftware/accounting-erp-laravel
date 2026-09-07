<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsReceivableApi;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\AccountsReceivable\Models\ReceivableDispute;
use Liberu\Accounting\AccountsReceivable\Models\ReceivableOpenItem;
use Liberu\Accounting\AccountsReceivable\Models\ReceivableReceipt;
use Liberu\Accounting\AccountsReceivableApi\Policies\AccountsReceivablePolicy;

class AccountsReceivableApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(ReceivableOpenItem::class, AccountsReceivablePolicy::class);
        Gate::policy(ReceivableReceipt::class, AccountsReceivablePolicy::class);
        Gate::policy(ReceivableDispute::class, AccountsReceivablePolicy::class);
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
