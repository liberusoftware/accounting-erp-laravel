<?php

declare(strict_types=1);

namespace Liberu\Accounting\Reimbursements;

use Illuminate\Support\ServiceProvider;

final class ReimbursementsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
