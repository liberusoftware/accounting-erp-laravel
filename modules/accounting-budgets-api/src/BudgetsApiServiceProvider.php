<?php

declare(strict_types=1);

namespace Liberu\Accounting\BudgetsApi;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\Budgets\Models\Budget;
use Liberu\Accounting\BudgetsApi\Policies\BudgetsPolicy;

final class BudgetsApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(Budget::class, BudgetsPolicy::class);
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
