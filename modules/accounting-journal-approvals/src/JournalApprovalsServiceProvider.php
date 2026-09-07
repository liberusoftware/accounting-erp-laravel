<?php

declare(strict_types=1);

namespace Liberu\Accounting\JournalApprovals;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\JournalApprovals\Queries\JournalApprovalQuery;

final class JournalApprovalsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(JournalApprovalQuery::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
