<?php

declare(strict_types=1);

namespace Liberu\Accounting\MatchingIntelligence;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\MatchingIntelligence\Queries\MatchingQuery;

final class MatchingIntelligenceServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    public function register(): void
    {
        $this->app->singleton(MatchingQuery::class);
    }
}
