<?php

declare(strict_types=1);

namespace Liberu\Accounting\CashCodingApi;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\CashCoding\Models\CashCodingBatch;
use Liberu\Accounting\CashCodingApi\Policies\CashCodingPolicy;

final class CashCodingApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(CashCodingBatch::class, CashCodingPolicy::class);
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
