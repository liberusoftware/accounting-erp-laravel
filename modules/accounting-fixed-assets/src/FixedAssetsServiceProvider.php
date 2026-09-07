<?php

declare(strict_types=1);

namespace Liberu\Accounting\FixedAssets;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\FixedAssets\Models\Asset;
use Liberu\Accounting\FixedAssets\Policies\AssetPolicy;
use Liberu\Accounting\FixedAssets\Queries\AssetQuery;

final class FixedAssetsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AssetQuery::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        Gate::policy(Asset::class, AssetPolicy::class);
    }
}
