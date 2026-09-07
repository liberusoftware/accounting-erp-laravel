<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesInvoicingApi;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\SalesInvoicing\Models\SalesInvoice;
use Liberu\Accounting\SalesInvoicingApi\Policies\SalesInvoicingPolicy;

final class SalesInvoicingApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(SalesInvoice::class, SalesInvoicingPolicy::class);
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
