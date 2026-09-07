<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountantWorkspaceApi;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\AccountantWorkspace\Models\WorkspaceItem;
use Liberu\Accounting\AccountantWorkspaceApi\Policies\WorkspacePolicy;

final class AccountantWorkspaceApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(WorkspaceItem::class, WorkspacePolicy::class);
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
