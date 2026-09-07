<?php

declare(strict_types=1);

namespace Liberu\Accounting\BusinessInsightsApi;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\BusinessInsights\Models\InsightSnapshot;
use Liberu\Accounting\BusinessInsightsApi\Policies\BusinessInsightsPolicy;

final class BusinessInsightsApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(InsightSnapshot::class, BusinessInsightsPolicy::class);
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
