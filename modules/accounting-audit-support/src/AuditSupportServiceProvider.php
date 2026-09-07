<?php

declare(strict_types=1);

namespace Liberu\Accounting\AuditSupport;

use Illuminate\Support\ServiceProvider;

final class AuditSupportServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
