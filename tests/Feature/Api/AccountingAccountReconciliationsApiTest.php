<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('supports account reconciliation API creation and lifecycle actions', function (): void {
    $user = User::factory()->create();
    $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Test Team', 'personal_team' => false]);
    $user->teams()->attach($team, ['role' => 'admin']);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.account-reconciliations.read', 'accounting.account-reconciliations.write']);

    $response = $this->postJson('/api/v1/accounting/account-reconciliations', [
        'book_id' => 10,
        'account_id' => 200,
        'period_start' => '2026-08-01',
        'period_end' => '2026-08-31',
    ])->assertCreated();
    $id = $response->json('data.id');
    expect($response->json('data.attributes.team_id'))->toBe($team->id)
        ->and($user->fresh()->current_team_id)->toBe($team->id);

    $this->postJson("/api/v1/accounting/account-reconciliations/{$id}/prepare", [
        'source_balance' => ['amount' => 1250.50, 'currency' => 'USD'],
    ])->assertOk();
    expect(DB::table('accounting_account_reconciliations')->where('id', $id)->value('status'))->toBe('prepared');
    $this->postJson("/api/v1/accounting/account-reconciliations/{$id}/review", ['comment' => 'Reviewed'])->assertOk();
    $this->postJson("/api/v1/accounting/account-reconciliations/{$id}/certify", ['attestation' => 'Certified'])->assertOk();
    $this->postJson("/api/v1/accounting/account-reconciliations/{$id}/carry-forward", [
        'period_start' => '2026-09-01',
        'period_end' => '2026-09-30',
    ])->assertOk()->assertJsonPath('data.attributes.status', 'carried_forward');
});

it('denies account reconciliation reads without the read ability', function (): void {
    $user = User::factory()->create();
    $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Test Team', 'personal_team' => false]);
    $user->teams()->attach($team, ['role' => 'admin']);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.account-reconciliations.write']);

    $this->getJson('/api/v1/accounting/account-reconciliations')->assertForbidden();
});
