<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\PayrollIntegration\Models\PayrollImport;
use Liberu\Foundation\Organizations\Models\Team;

uses(RefreshDatabase::class);

function payrollIntegrationApiUser(): array
{
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.payroll-integration.read', 'accounting.payroll-integration.write']);

    return [$user, $team];
}

it('scopes imports and status changes to the current team', function (): void {
    [, $team] = payrollIntegrationApiUser();
    $otherTeam = Team::factory()->create();
    $other = PayrollImport::query()->create(['team_id' => $otherTeam->id, 'provider' => 'acme', 'period_start' => '2026-08-01', 'period_end' => '2026-08-31', 'run_ref' => 'RUN-2', 'currency' => 'GBP', 'payload_hash' => str_repeat('b', 64), 'status' => 'validated']);
    PayrollImport::query()->create(['team_id' => $team->id, 'provider' => 'acme', 'period_start' => '2026-08-01', 'period_end' => '2026-08-31', 'run_ref' => 'RUN-1', 'currency' => 'GBP', 'payload_hash' => str_repeat('a', 64), 'status' => 'validated']);

    $this->getJson('/api/v1/accounting/payroll-integration')->assertOk()->assertJsonCount(1, 'data');
    $this->postJson('/api/v1/accounting/payroll-integration/'.$other->id.'/status', ['status' => 'imported'])->assertNotFound();
});

it('uses the authenticated team and returns a resource when importing', function (): void {
    [, $team] = payrollIntegrationApiUser();
    $otherTeam = Team::factory()->create();

    $this->postJson('/api/v1/accounting/payroll-integration', ['team_id' => $otherTeam->id, 'provider' => 'acme', 'period_start' => '2026-08-01', 'period_end' => '2026-08-31', 'run_ref' => 'RUN-3', 'currency' => 'GBP', 'employee_refs' => ['EMP-1']])
        ->assertCreated()->assertJsonPath('data.provider', 'acme');

    expect(PayrollImport::query()->where('run_ref', 'RUN-3')->value('team_id'))->toBe($team->id);
});

it('resolves summary before the import wildcard', function (): void {
    payrollIntegrationApiUser();

    $this->getJson('/api/v1/accounting/payroll-integration/summary')->assertOk()->assertJsonStructure(['count', 'providers', 'validated', 'failed', 'imported', 'reconciled']);
});
