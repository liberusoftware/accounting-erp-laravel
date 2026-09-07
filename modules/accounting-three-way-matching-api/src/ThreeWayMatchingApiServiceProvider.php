<?php

declare(strict_types=1);

namespace Liberu\Accounting\ThreeWayMatchingApi;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\ThreeWayMatching\Models\MatchRecord;
use Liberu\Accounting\ThreeWayMatchingApi\Policies\ThreeWayMatchingPolicy;

final class ThreeWayMatchingApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(MatchRecord::class, ThreeWayMatchingPolicy::class);
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
