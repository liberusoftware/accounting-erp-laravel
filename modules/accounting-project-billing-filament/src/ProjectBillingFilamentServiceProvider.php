<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectBillingFilament;

use Illuminate\Support\ServiceProvider;

final class ProjectBillingFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ProjectBillingFilamentPlugin::class);
    }
}
