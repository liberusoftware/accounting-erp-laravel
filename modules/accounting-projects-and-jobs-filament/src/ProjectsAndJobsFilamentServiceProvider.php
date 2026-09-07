<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectsAndJobsFilament;

use Illuminate\Support\ServiceProvider;

final class ProjectsAndJobsFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ProjectsAndJobsFilamentPlugin::class);
    }
}
