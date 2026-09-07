<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\ProjectsAndJobs\Models\ProjectJob;
use Liberu\Foundation\Organizations\Models\Team;

uses(RefreshDatabase::class);

it('scopes projects and jobs to the current team and ignores supplied team ids', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $otherTeam = Team::factory()->create();
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.projects-and-jobs.read', 'accounting.projects-and-jobs.write']);
    $other = ProjectJob::query()->create(['team_id' => $otherTeam->id, 'name' => 'Other project', 'status' => 'draft']);

    $this->getJson('/api/v1/accounting/projects-and-jobs')->assertOk()->assertJsonCount(0, 'data');
    $this->getJson('/api/v1/accounting/projects-and-jobs/'.$other->id)->assertNotFound();
    $this->postJson('/api/v1/accounting/projects-and-jobs', ['team_id' => $otherTeam->id, 'name' => 'Current project'])->assertCreated();

    expect(ProjectJob::query()->where('name', 'Current project')->value('team_id'))->toBe($team->id);
});

it('requires the projects and jobs write ability', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.projects-and-jobs.read']);

    $this->postJson('/api/v1/accounting/projects-and-jobs', ['name' => 'Read-only project'])->assertForbidden();
});
