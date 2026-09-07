<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('creates corporate cards and transactions through the API', function (): void {
    $user = User::factory()->create();
    $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Cards API', 'personal_team' => false]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.corporate-cards.read', 'accounting.corporate-cards.write']);
    $account = $this->postJson('/api/v1/accounting/corporate-cards', ['card_ref' => 'API-CARD', 'holder_ref' => 'user-api', 'currency' => 'GBP', 'limit_amount' => 1000])->assertCreated()->json('data');
    $this->postJson("/api/v1/accounting/corporate-cards/{$account['id']}/transactions", ['transaction_ref' => 'API-TX', 'transaction_date' => '2026-08-29', 'amount' => 100, 'currency' => 'GBP'])->assertCreated();
    $this->getJson('/api/v1/accounting/corporate-cards')->assertOk()->assertJsonCount(1, 'data');
});
