<?php

declare(strict_types=1);

namespace Liberu\Accounting\TaxCoreApi;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\TaxCore\Models\TaxRule;
use Liberu\Accounting\TaxCoreApi\Policies\TaxCorePolicy;

final class TaxCoreApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(TaxRule::class, TaxCorePolicy::class);
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
