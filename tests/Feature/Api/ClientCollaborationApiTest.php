<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('creates and lists client collaboration threads through the API', function (): void {
    $user = User::factory()->create();
    $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Collaboration API', 'personal_team' => false]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.client-collaboration.read', 'accounting.client-collaboration.write']);

    $this->postJson('/api/v1/accounting/client-collaboration', ['thread_ref' => 'api-thread', 'kind' => 'question', 'subject' => 'Question'])->assertCreated();
    $this->getJson('/api/v1/accounting/client-collaboration')->assertOk()->assertJsonCount(1, 'data');
});
