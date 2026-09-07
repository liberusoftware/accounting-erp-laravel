<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('creates and adjusts a year-end period through the API', function (): void {
    $user = User::factory()->create();
    $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Year End API', 'personal_team' => false]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.year-end.read', 'accounting.year-end.write']);
    $period = $this->postJson('/api/v1/accounting/year-end', ['period_ref' => '2026', 'period_end' => '2026-12-31'])->assertCreated()->json('data');
    $this->postJson("/api/v1/accounting/year-end/{$period['id']}/adjustments", ['adjustment_ref' => 'API-ADJ', 'amount' => 100, 'description' => 'Accrual'])->assertCreated();
    $this->getJson('/api/v1/accounting/year-end')->assertOk()->assertJsonCount(1, 'data');
});
