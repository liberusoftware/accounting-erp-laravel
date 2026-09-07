<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\ProjectCosting\Models\ProjectCost;
use Liberu\Foundation\Organizations\Models\Team;

uses(RefreshDatabase::class);

it('scopes project costs and summaries to the current team', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $otherTeam = Team::factory()->create();
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.project-costing.read', 'accounting.project-costing.write']);
    $other = ProjectCost::query()->create(['team_id' => $otherTeam->id, 'project_job_id' => 20, 'type' => 'labor', 'occurred_on' => '2026-01-01', 'amount' => 10, 'currency' => 'GBP']);

    $this->getJson('/api/v1/accounting/project-costing')->assertOk()->assertJsonCount(0, 'data');
    $this->getJson('/api/v1/accounting/project-costing/'.$other->id)->assertNotFound();
    $this->getJson('/api/v1/accounting/project-costing/project/20/summary')->assertOk()->assertJsonPath('entries', 0);
    $this->postJson('/api/v1/accounting/project-costing', ['team_id' => $otherTeam->id, 'project_job_id' => 10, 'type' => 'labor', 'amount' => 25, 'currency' => 'GBP'])->assertCreated();

    expect(ProjectCost::query()->where('project_job_id', 10)->value('team_id'))->toBe($team->id);
});

it('requires the project costing write ability', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.project-costing.read']);

    $this->postJson('/api/v1/accounting/project-costing', ['project_job_id' => 10, 'type' => 'labor', 'amount' => 25, 'currency' => 'GBP'])->assertForbidden();
});
