<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankReconciliationApi;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\BankReconciliation\Models\ReconciliationSession;
use Liberu\Accounting\BankReconciliationApi\Policies\BankReconciliationPolicy;

final class BankReconciliationApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(ReconciliationSession::class, BankReconciliationPolicy::class);
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
