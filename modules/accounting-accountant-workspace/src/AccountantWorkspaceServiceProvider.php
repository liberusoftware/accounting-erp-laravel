<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountantWorkspace;

use Illuminate\Support\ServiceProvider;

final class AccountantWorkspaceServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
