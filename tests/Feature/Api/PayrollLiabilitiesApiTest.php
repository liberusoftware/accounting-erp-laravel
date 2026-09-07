<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\PayrollLiabilities\Models\PayrollLiability;
use Liberu\Foundation\Organizations\Models\Team;

uses(RefreshDatabase::class);

function payrollLiabilitiesApiUser(): array
{
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.payroll-liabilities.read', 'accounting.payroll-liabilities.write']);

    return [$user, $team];
}

it('scopes liabilities and detail endpoints to the current team', function (): void {
    [, $team] = payrollLiabilitiesApiUser();
    $otherTeam = Team::factory()->create();
    PayrollLiability::query()->create(['team_id' => $team->id, 'liability_ref' => 'TAX-1', 'currency' => 'GBP', 'amount' => 500]);
    $other = PayrollLiability::query()->create(['team_id' => $otherTeam->id, 'liability_ref' => 'TAX-2', 'currency' => 'GBP', 'amount' => 700]);

    $this->getJson('/api/v1/accounting/payroll-liabilities')->assertOk()->assertJsonCount(1, 'data');
    $this->getJson('/api/v1/accounting/payroll-liabilities/'.$other->id)->assertNotFound();
});

it('uses the authenticated team when recording a liability', function (): void {
    [, $team] = payrollLiabilitiesApiUser();
    $otherTeam = Team::factory()->create();

    $this->postJson('/api/v1/accounting/payroll-liabilities', ['team_id' => $otherTeam->id, 'liability_ref' => 'TAX-3', 'currency' => 'GBP', 'amount' => 500])
        ->assertCreated();

    expect(PayrollLiability::query()->where('liability_ref', 'TAX-3')->value('team_id'))->toBe($team->id);
});

it('protects allocation and summary routes', function (): void {
    [, $team] = payrollLiabilitiesApiUser();
    $liability = PayrollLiability::query()->create(['team_id' => $team->id, 'liability_ref' => 'TAX-4', 'currency' => 'GBP', 'amount' => 500]);

    $this->postJson('/api/v1/accounting/payroll-liabilities/'.$liability->id.'/allocate', ['amount' => 100, 'allocation_ref' => 'PAY-1'])
        ->assertOk()->assertJsonPath('data.outstanding', 400);
    $this->getJson('/api/v1/accounting/payroll-liabilities/summary')->assertOk()->assertJsonStructure(['count', 'amount', 'paid', 'outstanding', 'overdue']);
});
