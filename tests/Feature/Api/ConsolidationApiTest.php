<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('creates and lists consolidation groups through the API', function (): void {
    $user = User::factory()->create();
    $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Consolidation API', 'personal_team' => false]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.consolidation.read', 'accounting.consolidation.write']);

    $this->postJson('/api/v1/accounting/consolidation', ['group_ref' => 'api-group', 'name' => 'API Group', 'reporting_currency' => 'GBP'])->assertCreated();
    $this->getJson('/api/v1/accounting/consolidation')->assertOk()->assertJsonCount(1, 'data');
});
