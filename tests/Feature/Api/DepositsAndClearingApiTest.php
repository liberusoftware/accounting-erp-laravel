<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('records and groups clearing funds through the API', function (): void {
    $user = User::factory()->create();
    $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Clearing API', 'personal_team' => false]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.deposits-and-clearing.read', 'accounting.deposits-and-clearing.write']);
    $fund = $this->postJson('/api/v1/accounting/deposits-and-clearing/funds', ['source_type' => 'payment', 'source_id' => 'api-1', 'amount' => 75, 'currency' => 'GBP', 'received_on' => '2026-08-29'])->assertCreated()->json('data');
    $this->postJson('/api/v1/accounting/deposits-and-clearing/deposits', ['deposit_ref' => 'API-DEP', 'account_ref' => 'bank', 'currency' => 'GBP', 'deposit_date' => '2026-08-29', 'fund_ids' => [$fund['id']]])->assertCreated();
    $this->getJson('/api/v1/accounting/deposits-and-clearing/deposits')->assertOk()->assertJsonCount(1, 'data');
});

it('requires clearing write ability', function (): void {
    $user = User::factory()->create();
    $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Read only clearing', 'personal_team' => false]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.deposits-and-clearing.read']);
    $this->postJson('/api/v1/accounting/deposits-and-clearing/funds', [])->assertForbidden();
});
