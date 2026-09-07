<?php

declare(strict_types=1);

namespace Liberu\Accounting\DimensionsApi;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\Dimensions\Models\Dimension;
use Liberu\Accounting\DimensionsApi\Policies\DimensionsPolicy;

final class DimensionsApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(Dimension::class, DimensionsPolicy::class);
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
