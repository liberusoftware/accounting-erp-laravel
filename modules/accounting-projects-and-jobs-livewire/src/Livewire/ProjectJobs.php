<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectsAndJobsLivewire\Livewire;

use Illuminate\Auth\Access\AuthorizationException;
use Liberu\Accounting\ProjectsAndJobs\Models\ProjectJob;
use Livewire\Component;
use Livewire\WithPagination;

final class ProjectJobs extends Component
{
    use WithPagination;

    public function mount(): void
    {
        if (! auth()->check()) {
            throw new AuthorizationException('Authentication is required to view projects and jobs.');
        }
    }

    public function render(): mixed
    {
        return view('module-accounting-projects-and-jobs::project-jobs', ['projects' => ProjectJob::query()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1))->latest()->paginate(15)]);
    }
}
