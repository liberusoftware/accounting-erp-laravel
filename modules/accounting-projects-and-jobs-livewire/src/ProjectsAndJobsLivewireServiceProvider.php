<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectsAndJobsLivewire;

use Illuminate\Support\ServiceProvider;
use Liberu\Accounting\ProjectsAndJobsLivewire\Livewire\ProjectJobs;
use Livewire\Livewire;

final class ProjectsAndJobsLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component('accounting-project-jobs', ProjectJobs::class);
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'module-accounting-projects-and-jobs');
    }
}
