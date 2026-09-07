<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\ProjectsAndJobs\Actions\CreateProjectJob;
use Liberu\Accounting\ProjectsAndJobs\Actions\TransitionProject;
use Liberu\Accounting\ProjectsAndJobs\Enums\ProjectStatus;
use Liberu\Accounting\ProjectsAndJobs\Exceptions\InvalidProject;

uses(RefreshDatabase::class);
it('creates hierarchical projects with legacy fields and completes their lifecycle', function (): void {
    $parent = app(CreateProjectJob::class)->handle(['name' => 'Implementation', 'code' => 'IMP-1', 'start_date' => '2026-01-01', 'budget_amount' => 10000, 'budget_currency' => 'USD']);
    $child = app(CreateProjectJob::class)->handle(['name' => 'Discovery job', 'parent_id' => $parent->id, 'manager_ref' => 'manager-1', 'dimensions' => ['department' => 'delivery'], 'source_links' => ['crm' => 'CRM-1']]);
    app(TransitionProject::class)->handle($parent, ProjectStatus::Active);
    app(TransitionProject::class)->handle($parent->refresh(), ProjectStatus::Completed);
    $archived = app(TransitionProject::class)->handle($parent->refresh(), ProjectStatus::Archived);
    expect($archived->status)->toBe(ProjectStatus::Archived)->and($archived->children)->toHaveCount(1)->and($child->source_links['crm'])->toBe('CRM-1');
});
it('rejects invalid dates and illegal lifecycle transitions', function (): void {
    expect(fn () => app(CreateProjectJob::class)->handle(['name' => 'Bad', 'start_date' => '2026-02-01', 'end_date' => '2026-01-01']))->toThrow(InvalidProject::class);
    $project = app(CreateProjectJob::class)->handle(['name' => 'Draft']);
    expect(fn () => app(TransitionProject::class)->handle($project, ProjectStatus::Completed))->toThrow(InvalidProject::class);
});
