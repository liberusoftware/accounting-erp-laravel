<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('creates and lists collection cases through the API', function (): void {
    $user = User::factory()->create();
    $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Collections API', 'personal_team' => false]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.collections.read', 'accounting.collections.write']);

    $this->postJson('/api/v1/accounting/collections', ['case_ref' => 'api-case', 'customer_ref' => 'api-customer', 'balance' => 250])->assertCreated();
    $this->getJson('/api/v1/accounting/collections')->assertOk()->assertJsonCount(1, 'data');
});
