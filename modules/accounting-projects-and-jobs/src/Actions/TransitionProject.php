<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectsAndJobs\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\ProjectsAndJobs\Enums\ProjectStatus;
use Liberu\Accounting\ProjectsAndJobs\Exceptions\InvalidProject;
use Liberu\Accounting\ProjectsAndJobs\Models\ProjectJob;

final class TransitionProject
{
    public function handle(ProjectJob $project, ProjectStatus $status): ProjectJob
    {
        $valid = match ($project->status) {
            ProjectStatus::Draft => [ProjectStatus::Active, ProjectStatus::Cancelled],ProjectStatus::Active => [ProjectStatus::OnHold, ProjectStatus::Completed, ProjectStatus::Cancelled],ProjectStatus::OnHold => [ProjectStatus::Active, ProjectStatus::Cancelled],ProjectStatus::Completed => [ProjectStatus::Archived],default => []
        };
        if (! in_array($status, $valid, true)) {
            throw new InvalidProject("Cannot transition from {$project->status->value} to {$status->value}.");
        }

        return DB::transaction(fn (): ProjectJob => tap($project)->update(['status' => $status]));
    }
}
