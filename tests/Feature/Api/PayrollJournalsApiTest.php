<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\PayrollJournals\Models\PayrollJournal;
use Liberu\Foundation\Organizations\Models\Team;

uses(RefreshDatabase::class);

function payrollJournalsApiUser(): array
{
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.payroll-journals.read', 'accounting.payroll-journals.write']);

    return [$user, $team];
}

it('scopes payroll journals to the authenticated current team', function (): void {
    [, $team] = payrollJournalsApiUser();
    $otherTeam = Team::factory()->create();

    PayrollJournal::query()->create([
        'team_id' => $team->id,
        'journal_ref' => 'TEAM-1',
        'payroll_period_start' => '2026-01-01',
        'payroll_period_end' => '2026-01-31',
        'currency' => 'GBP',
        'gross_pay' => 100,
        'taxes' => 10,
        'deductions' => 0,
        'net_pay' => 90,
    ]);
    PayrollJournal::query()->create([
        'team_id' => $otherTeam->id,
        'journal_ref' => 'TEAM-2',
        'payroll_period_start' => '2026-01-01',
        'payroll_period_end' => '2026-01-31',
        'currency' => 'GBP',
        'gross_pay' => 100,
        'taxes' => 10,
        'deductions' => 0,
        'net_pay' => 90,
    ]);

    $this->getJson('/api/v1/accounting/payroll-journals')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.attributes.journal_ref', 'TEAM-1');
});

it('uses the authenticated current team when creating a payroll journal', function (): void {
    [, $team] = payrollJournalsApiUser();
    $otherTeam = Team::factory()->create();

    $this->postJson('/api/v1/accounting/payroll-journals', [
        'team_id' => $otherTeam->id,
        'journal_ref' => 'PAY-1',
        'payroll_period_start' => '2026-01-01',
        'payroll_period_end' => '2026-01-31',
        'currency' => 'GBP',
        'gross_pay' => 100,
        'taxes' => 10,
        'deductions' => 0,
    ])->assertCreated();

    expect(PayrollJournal::query()->where('journal_ref', 'PAY-1')->value('team_id'))
        ->toBe($team->id);
});

it('resolves the payroll journal summary before the journal wildcard', function (): void {
    payrollJournalsApiUser();

    $this->getJson('/api/v1/accounting/payroll-journals/summary')
        ->assertOk()
        ->assertJsonStructure(['count', 'gross', 'taxes', 'deductions', 'net_pay', 'employer_costs', 'posted', 'reversed']);
});
