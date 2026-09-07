<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectProfitabilityFilament;

use Illuminate\Support\ServiceProvider;

final class ProjectProfitabilityFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ProjectProfitabilityFilamentPlugin::class);
    }
}
