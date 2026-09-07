<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankRulesApi;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\BankRules\Models\BankRule;
use Liberu\Accounting\BankRulesApi\Policies\BankRulesPolicy;

final class BankRulesApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(BankRule::class, BankRulesPolicy::class);
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
