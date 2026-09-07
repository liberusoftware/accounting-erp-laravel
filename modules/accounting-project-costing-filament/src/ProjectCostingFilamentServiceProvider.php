<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectCostingFilament;

use Illuminate\Support\ServiceProvider;

final class ProjectCostingFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ProjectCostingFilamentPlugin::class);
    }
}
