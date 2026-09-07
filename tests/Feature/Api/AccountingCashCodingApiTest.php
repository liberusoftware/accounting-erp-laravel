<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('supports authorized cash coding batch transitions', function (): void {
    $user = User::factory()->create();
    $user->forceFill(['current_team_id' => 101])->save();
    Sanctum::actingAs($user, ['accounting.cash-coding.read', 'accounting.cash-coding.write']);
    $response = $this->postJson('/api/v1/accounting/cash-coding', ['reference' => 'API-CASH-1', 'currency' => 'USD', 'lines' => [['source_reference' => 'txn-api', 'amount' => 45, 'currency' => 'USD', 'account_id' => '7000']]])->assertCreated();
    $id = $response->json('data.id');
    $this->postJson("/api/v1/accounting/cash-coding/{$id}/review")->assertOk()->assertJsonPath('data.attributes.status', 'in_review');
    $this->postJson("/api/v1/accounting/cash-coding/{$id}/post")->assertOk()->assertJsonPath('data.attributes.status', 'posted');
    $this->postJson("/api/v1/accounting/cash-coding/{$id}/undo", ['reason' => 'Recode'])->assertOk()->assertJsonPath('data.attributes.status', 'undone');
});

it('denies cash coding reads without the read ability', function (): void {
    $user = User::factory()->create();
    $user->forceFill(['current_team_id' => 101])->save();
    Sanctum::actingAs($user, ['accounting.cash-coding.write']);
    $this->getJson('/api/v1/accounting/cash-coding')->assertForbidden();
});
