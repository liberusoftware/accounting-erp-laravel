<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectsAndJobs;

use Illuminate\Support\ServiceProvider;

final class ProjectsAndJobsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
