<?php

declare(strict_types=1);

namespace Liberu\Accounting\AssetEventsApi;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\AssetEvents\Models\AssetEvent;
use Liberu\Accounting\AssetEventsApi\Policies\AssetEventPolicy;

final class AssetEventsApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(AssetEvent::class, AssetEventPolicy::class);
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
