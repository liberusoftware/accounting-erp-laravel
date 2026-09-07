<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\ProjectBilling\Models\ProjectBilling;
use Liberu\Foundation\Organizations\Models\Team;

uses(RefreshDatabase::class);

it('scopes project billing to the current team and ignores a supplied team id', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $otherTeam = Team::factory()->create();
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.project-billing.read', 'accounting.project-billing.write']);
    $other = ProjectBilling::query()->create(['team_id' => $otherTeam->id, 'project_job_id' => 20, 'method' => 'fixed_fee', 'currency' => 'GBP', 'period_start' => '2026-01-01', 'period_end' => '2026-01-31']);

    $this->getJson('/api/v1/accounting/project-billing')->assertOk()->assertJsonCount(0, 'data');
    $this->getJson('/api/v1/accounting/project-billing/'.$other->id)->assertNotFound();
    $this->postJson('/api/v1/accounting/project-billing', ['team_id' => $otherTeam->id, 'project_job_id' => 10, 'method' => 'fixed_fee', 'currency' => 'GBP'])->assertCreated();

    expect(ProjectBilling::query()->where('project_job_id', 10)->value('team_id'))->toBe($team->id);
});

it('requires the project billing write ability', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.project-billing.read']);

    $this->postJson('/api/v1/accounting/project-billing', ['project_job_id' => 10, 'method' => 'fixed_fee', 'currency' => 'GBP'])->assertForbidden();
});
