<?php

declare(strict_types=1);

namespace Liberu\Accounting\BillPaymentsApi;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\BillPayments\Models\BillPaymentProposal;
use Liberu\Accounting\BillPaymentsApi\Policies\BillPaymentsPolicy;

final class BillPaymentsApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(BillPaymentProposal::class, BillPaymentsPolicy::class);
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
