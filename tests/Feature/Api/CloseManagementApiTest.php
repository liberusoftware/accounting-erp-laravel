<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\Erp\Models\Team;
use Liberu\Accounting\Erp\Models\User;

uses(RefreshDatabase::class);

it('creates and lists close cycles through the API', function (): void {
    $user = User::factory()->create();
    $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Close API', 'personal_team' => false]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.close-management.read', 'accounting.close-management.write']);

    $this->postJson('/api/v1/accounting/close-management', ['cycle_ref' => 'api-close', 'period' => '2026-08', 'due_date' => '2026-09-10'])->assertCreated();
    $this->getJson('/api/v1/accounting/close-management')->assertOk()->assertJsonCount(1, 'data');
});
