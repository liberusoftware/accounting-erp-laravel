<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('supports authorized bank rule CRUD and dry-run testing', function (): void {
    $user = User::factory()->create();
    $user->forceFill(['current_team_id' => 81])->save();
    Sanctum::actingAs($user, ['accounting.bank-rules.read', 'accounting.bank-rules.write']);

    $response = $this->postJson('/api/v1/accounting/bank-rules', [
        'name' => 'Subscriptions', 'priority' => 5, 'conditions' => ['text' => 'subscription'], 'actions' => ['category' => 'software'], 'automation_mode' => 'suggest',
    ])->assertCreated();
    $id = $response->json('data.id');

    $this->postJson("/api/v1/accounting/bank-rules/{$id}/test", ['transaction' => ['description' => 'Monthly subscription', 'amount' => 12]])
        ->assertOk()->assertJsonPath('data.matched', true);
    $this->patchJson("/api/v1/accounting/bank-rules/{$id}", ['name' => 'Subscriptions', 'conditions' => ['text' => 'saas'], 'actions' => ['category' => 'software'], 'priority' => 8])
        ->assertOk()->assertJsonPath('data.attributes.priority', 8);
    $this->deleteJson("/api/v1/accounting/bank-rules/{$id}")->assertNoContent();
});

it('denies bank rule access without the read ability', function (): void {
    $user = User::factory()->create();
    $user->forceFill(['current_team_id' => 81])->save();
    Sanctum::actingAs($user, ['accounting.bank-rules.write']);

    $this->getJson('/api/v1/accounting/bank-rules')->assertForbidden();
});
