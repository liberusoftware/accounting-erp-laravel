<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\ProjectProfitability\Models\ProjectProfitability;
use Liberu\Foundation\Organizations\Models\Team;

uses(RefreshDatabase::class);

function projectProfitabilityApiUser(array $abilities = ['accounting.project-profitability.read', 'accounting.project-profitability.write']): array
{
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, $abilities);

    return [$user, $team];
}

it('scopes project profitability records and dashboards to the current team', function (): void {
    [, $team] = projectProfitabilityApiUser();
    $otherTeam = Team::factory()->create();
    $other = ProjectProfitability::query()->create(['team_id' => $otherTeam->id, 'project_job_id' => 20, 'period_start' => '2026-01-01', 'period_end' => '2026-01-31', 'currency' => 'GBP']);
    ProjectProfitability::query()->create(['team_id' => $team->id, 'project_job_id' => 10, 'period_start' => '2026-01-01', 'period_end' => '2026-01-31', 'currency' => 'GBP']);

    $this->getJson('/api/v1/accounting/project-profitability')->assertOk()->assertJsonCount(1, 'data');
    $this->getJson('/api/v1/accounting/project-profitability/'.$other->id)->assertNotFound();
    $this->getJson('/api/v1/accounting/project-profitability/project/20/dashboard')->assertOk()->assertJsonPath('periods', 0);
});

it('uses the authenticated team and requires write access when recording profitability', function (): void {
    [, $team] = projectProfitabilityApiUser();
    $otherTeam = Team::factory()->create();

    $this->postJson('/api/v1/accounting/project-profitability', ['team_id' => $otherTeam->id, 'project_job_id' => 10, 'period_start' => '2026-02-01', 'period_end' => '2026-02-28', 'currency' => 'GBP'])
        ->assertCreated();

    expect(ProjectProfitability::query()->where('project_job_id', 10)->value('team_id'))->toBe($team->id);

    [$readOnlyUser, $readOnlyTeam] = projectProfitabilityApiUser(['accounting.project-profitability.read']);
    $this->postJson('/api/v1/accounting/project-profitability', ['project_job_id' => 11, 'period_start' => '2026-02-01', 'period_end' => '2026-02-28', 'currency' => 'GBP'])->assertForbidden();
    expect($readOnlyUser->current_team_id)->toBe($readOnlyTeam->id);
});
