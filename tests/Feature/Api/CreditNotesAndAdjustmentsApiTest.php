<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('creates and approves credit notes through the API', function (): void {
    $user = User::factory()->create();
    $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Credits API', 'personal_team' => false]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.credit-notes-and-adjustments.read', 'accounting.credit-notes-and-adjustments.write']);
    $note = $this->postJson('/api/v1/accounting/credit-notes-and-adjustments', ['customer_id' => 'cust-api', 'credit_ref' => 'API-CN', 'reason' => 'Return', 'currency' => 'GBP', 'amount' => 75])->assertCreated()->json('data');
    $this->postJson("/api/v1/accounting/credit-notes-and-adjustments/{$note['id']}/approve")->assertOk();
    $this->getJson('/api/v1/accounting/credit-notes-and-adjustments')->assertOk()->assertJsonCount(1, 'data');
});
