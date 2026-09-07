<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('creates and lists cash positions through the API', function (): void {
    $user = User::factory()->create();
    $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Cash API', 'personal_team' => false]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.cash-position.read', 'accounting.cash-position.write']);

    $this->postJson('/api/v1/accounting/cash-position', ['view_ref' => 'api-view', 'currency' => 'USD', 'ledger_balance' => 500])->assertCreated();
    $this->getJson('/api/v1/accounting/cash-position')->assertOk()->assertJsonCount(1, 'data');
});
