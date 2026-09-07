<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountReconciliationsApi;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\AccountReconciliations\Models\AccountReconciliation;
use Liberu\Accounting\AccountReconciliationsApi\Policies\AccountReconciliationsPolicy;

final class AccountReconciliationsApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(AccountReconciliation::class, AccountReconciliationsPolicy::class);
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
