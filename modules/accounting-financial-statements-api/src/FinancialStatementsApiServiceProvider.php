<?php

declare(strict_types=1);

namespace Liberu\Accounting\FinancialStatementsApi;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

final class FinancialStatementsApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        Gate::define('accounting.financial-statements.view', static fn ($user): bool => $user !== null
            && method_exists($user, 'tokenCan')
            && $user->tokenCan('accounting.financial-statements.read'));

    }
}
