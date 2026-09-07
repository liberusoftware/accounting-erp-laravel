<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\WithholdingTax\Models\WithholdingTaxLiability;
use Liberu\Accounting\WithholdingTax\Models\WithholdingTaxRule;
use Liberu\Foundation\Organizations\Models\Team;

uses(RefreshDatabase::class);

it('scopes withholding tax rules and deductions to the current team', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $otherTeam = Team::factory()->create();
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.withholding-tax.read', 'accounting.withholding-tax.write']);

    $this->postJson('/api/v1/accounting/withholding-tax/rules', [
        'team_id' => $otherTeam->id,
        'code' => 'WHT-10',
        'name' => 'Withholding ten',
        'jurisdiction' => 'CA',
        'rate' => 10,
        'effective_from' => '2026-01-01',
    ])->assertCreated();

    $rule = WithholdingTaxRule::query()->firstOrFail();
    expect($rule->team_id)->toBe($team->id);
    $this->getJson('/api/v1/accounting/withholding-tax/rules')->assertOk()->assertJsonCount(1, 'data');
    expect(WithholdingTaxRule::query()->where('team_id', $otherTeam->id)->count())->toBe(0);
});

it('calculates deductions and remits liabilities through the write API', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.withholding-tax.write']);

    $rule = WithholdingTaxRule::query()->create([
        'team_id' => $team->id,
        'code' => 'WHT-10',
        'name' => 'Withholding ten',
        'jurisdiction' => 'CA',
        'rate' => 10,
        'effective_from' => '2026-01-01',
        'status' => 'draft',
    ]);

    $deductionResponse = $this->postJson('/api/v1/accounting/withholding-tax/rules/'.$rule->id.'/deductions', [
        'party_type' => 'supplier',
        'party_id' => 'supplier-1',
        'source_ref' => 'bill-1',
        'currency' => 'CAD',
        'gross_amount' => 250,
    ])->assertCreated();

    $deductionResponse->assertJsonPath('withheld_amount', '25.00');
    $liability = WithholdingTaxLiability::query()->create([
        'team_id' => $team->id,
        'deduction_id' => $deductionResponse->json('id'),
        'amount' => 25,
        'due_on' => '2026-02-15',
        'status' => 'open',
    ]);

    $this->postJson('/api/v1/accounting/withholding-tax/liabilities/'.$liability->id.'/remit', [
        'amount' => 25,
        'remitted_on' => '2026-02-02',
        'reference' => 'remit-1',
    ])->assertCreated();
});

it('requires withholding tax abilities', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, []);

    $this->getJson('/api/v1/accounting/withholding-tax/rules')->assertForbidden();
    $this->postJson('/api/v1/accounting/withholding-tax/rules', [])->assertForbidden();
});
