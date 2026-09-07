<?php

declare(strict_types=1);

namespace Liberu\Accounting\FinancialMasterDataApi;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\FinancialMasterData\Models\Party;
use Liberu\Accounting\FinancialMasterDataApi\Policies\PartyPolicy;

final class FinancialMasterDataApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(Party::class, PartyPolicy::class);
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
