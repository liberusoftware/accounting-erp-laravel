<?php

declare(strict_types=1);

namespace Liberu\Accounting\SupplierBillsApi;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\SupplierBills\Models\SupplierBill;
use Liberu\Accounting\SupplierBillsApi\Policies\SupplierBillsPolicy;

final class SupplierBillsApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(SupplierBill::class, SupplierBillsPolicy::class);
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
