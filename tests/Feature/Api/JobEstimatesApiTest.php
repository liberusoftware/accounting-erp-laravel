<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\JobEstimates\Models\JobEstimate;
use Liberu\Foundation\Organizations\Models\Team;

uses(RefreshDatabase::class);

function jobEstimatesApiUser(): array
{
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.job-estimates.read', 'accounting.job-estimates.write']);

    return [$user, $team];
}

it('scopes job estimates and comparison routes to the current team', function (): void {
    [, $team] = jobEstimatesApiUser();
    $otherTeam = Team::factory()->create();
    $other = JobEstimate::query()->create(['team_id' => $otherTeam->id, 'estimate_ref' => 'EST-2', 'project_ref' => 'JOB-2', 'title' => 'Other', 'currency' => 'GBP', 'status' => 'draft', 'version_no' => 1]);
    JobEstimate::query()->create(['team_id' => $team->id, 'estimate_ref' => 'EST-1', 'project_ref' => 'JOB-1', 'title' => 'Current', 'currency' => 'GBP', 'status' => 'draft', 'version_no' => 1]);

    $this->getJson('/api/v1/accounting/job-estimates')->assertOk()->assertJsonCount(1, 'data');
    $this->getJson('/api/v1/accounting/job-estimates/'.$other->id.'/comparison')->assertNotFound();
});

it('uses the authenticated team when creating a job estimate', function (): void {
    [, $team] = jobEstimatesApiUser();
    $otherTeam = Team::factory()->create();

    $this->postJson('/api/v1/accounting/job-estimates', ['team_id' => $otherTeam->id, 'estimate_ref' => 'EST-3', 'project_ref' => 'JOB-3', 'title' => 'Current project', 'currency' => 'GBP'])
        ->assertCreated()->assertJsonPath('data.estimate_ref', 'EST-3');

    expect(JobEstimate::query()->where('estimate_ref', 'EST-3')->value('team_id'))->toBe($team->id);
});

it('requires the write ability for creating a job estimate', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.job-estimates.read']);

    $this->postJson('/api/v1/accounting/job-estimates', [
        'estimate_ref' => 'EST-4',
        'project_ref' => 'JOB-4',
        'title' => 'Read-only project',
        'currency' => 'GBP',
    ])->assertForbidden();
});
