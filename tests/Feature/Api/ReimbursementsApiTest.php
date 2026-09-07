<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\Reimbursements\Models\ReimbursementLiability;
use Liberu\Foundation\Organizations\Models\Team;

uses(RefreshDatabase::class);

it('scopes reimbursement liabilities and ignores supplied team ids', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $otherTeam = Team::factory()->create();
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.reimbursements.read', 'accounting.reimbursements.write']);
    ReimbursementLiability::query()->create(['team_id' => $otherTeam->id, 'payee_ref' => 'other', 'currency' => 'GBP', 'amount' => 10, 'status' => 'approved']);

    $this->getJson('/api/v1/accounting/reimbursements')->assertOk()->assertJsonCount(0, 'data');
    $this->postJson('/api/v1/accounting/reimbursements', ['team_id' => $otherTeam->id, 'payee_ref' => 'current', 'currency' => 'GBP', 'amount' => 25])->assertCreated();

    expect(ReimbursementLiability::query()->where('payee_ref', 'current')->value('team_id'))->toBe($team->id);
});

it('requires the reimbursements write ability', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.reimbursements.read']);

    $this->postJson('/api/v1/accounting/reimbursements', ['payee_ref' => 'read-only', 'currency' => 'GBP', 'amount' => 25])->assertForbidden();
});
