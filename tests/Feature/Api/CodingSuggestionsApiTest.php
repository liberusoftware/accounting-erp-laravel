<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('creates and lists coding suggestions through the API', function (): void {
    $user = User::factory()->create();
    $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Suggestions API', 'personal_team' => false]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.coding-suggestions.read', 'accounting.coding-suggestions.write']);

    $this->postJson('/api/v1/accounting/coding-suggestions', ['source_ref' => 'api-txn', 'target_type' => 'payee', 'target_ref' => 'api-payee', 'confidence' => .88, 'explanation' => 'Known payee'])->assertCreated();
    $this->getJson('/api/v1/accounting/coding-suggestions')->assertOk()->assertJsonCount(1, 'data');
});
