<?php

declare(strict_types=1);

namespace Liberu\Accounting\AuditSupportApi;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\AuditSupport\Models\AuditRequest;
use Liberu\Accounting\AuditSupportApi\Policies\AuditSupportPolicy;

final class AuditSupportApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(AuditRequest::class, AuditSupportPolicy::class);
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
