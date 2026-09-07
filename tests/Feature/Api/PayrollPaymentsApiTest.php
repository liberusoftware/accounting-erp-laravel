<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\PayrollPayments\Models\PayrollPaymentBatch;
use Liberu\Foundation\Organizations\Models\Team;

uses(RefreshDatabase::class);

function payrollApiUser(): array
{
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.payroll-payments.read', 'accounting.payroll-payments.write']);

    return [$user, $team];
}

it('scopes payroll payment batches to the authenticated current team', function (): void {
    [, $team] = payrollApiUser();
    $otherTeam = Team::factory()->create();

    PayrollPaymentBatch::query()->create(['team_id' => $team->id, 'batch_ref' => 'TEAM-1', 'currency' => 'GBP', 'net_pay_amount' => 100, 'liability_amount' => 20]);
    PayrollPaymentBatch::query()->create(['team_id' => $otherTeam->id, 'batch_ref' => 'TEAM-2', 'currency' => 'GBP', 'net_pay_amount' => 100, 'liability_amount' => 20]);

    $this->getJson('/api/v1/accounting/payroll-payments')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.attributes.batch_ref', 'TEAM-1');
});

it('does not trust a caller supplied team id when creating a batch', function (): void {
    [, $team] = payrollApiUser();
    $otherTeam = Team::factory()->create();

    $this->postJson('/api/v1/accounting/payroll-payments', [
        'team_id' => $otherTeam->id,
        'batch_ref' => 'PAY-1',
        'currency' => 'GBP',
        'net_pay_amount' => 100,
        'liability_amount' => 20,
    ])->assertCreated();

    expect(PayrollPaymentBatch::query()->where('batch_ref', 'PAY-1')->value('team_id'))
        ->toBe($team->id);
});

it('resolves the summary endpoint before the batch wildcard', function (): void {
    payrollApiUser();

    $this->getJson('/api/v1/accounting/payroll-payments/summary')
        ->assertOk()
        ->assertJsonStructure(['count', 'total_amount', 'pending', 'failed', 'reconciled']);
});
