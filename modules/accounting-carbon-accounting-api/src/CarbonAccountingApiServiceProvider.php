<?php

declare(strict_types=1);

namespace Liberu\Accounting\CarbonAccountingApi;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\CarbonAccounting\Models\CarbonActivity;
use Liberu\Accounting\CarbonAccountingApi\Policies\CarbonAccountingPolicy;

final class CarbonAccountingApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(CarbonActivity::class, CarbonAccountingPolicy::class);
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
