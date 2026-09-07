<?php

declare(strict_types=1);

namespace Liberu\Accounting\WorkforceCostingFilament;

use Illuminate\Support\ServiceProvider;

final class WorkforceCostingFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(WorkforceCostingFilamentPlugin::class, fn (): WorkforceCostingFilamentPlugin => WorkforceCostingFilamentPlugin::make());
    }
}
