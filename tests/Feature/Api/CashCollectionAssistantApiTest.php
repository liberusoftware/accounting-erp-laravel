<?php

declare(strict_types=1);
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);
it('creates and reads collection assistant cases through the tenant API', function (): void {
    $user = User::factory()->create();
    $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Collections API', 'personal_team' => false]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.cash-collection-assistant.read', 'accounting.cash-collection-assistant.write']);
    $this->postJson('/api/v1/accounting/cash-collection-assistant', ['invoice_ref' => 'INV-API', 'risk_score' => 75])->assertCreated();
    $this->getJson('/api/v1/accounting/cash-collection-assistant')->assertOk()->assertJsonCount(1, 'data');
});
