<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectsAndJobsApi;

use Illuminate\Support\ServiceProvider;

final class ProjectsAndJobsApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
