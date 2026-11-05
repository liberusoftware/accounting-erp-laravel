<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\Erp\Models\Team;
use Liberu\Accounting\Erp\Models\User;

uses(RefreshDatabase::class);

it('creates facilities and movements through the API', function (): void {
    $user = User::factory()->create();
    $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Debt API', 'personal_team' => false]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.debt-and-loans.read', 'accounting.debt-and-loans.write']);
    $facility = $this->postJson('/api/v1/accounting/debt-and-loans/facilities', ['facility_ref' => 'API-FAC', 'lender_ref' => 'BANK', 'currency' => 'GBP', 'limit_amount' => 5000, 'start_date' => '2026-08-29', 'maturity_date' => '2028-08-29'])->assertCreated()->json('data');
    $this->postJson("/api/v1/accounting/debt-and-loans/facilities/{$facility['id']}/movements", ['kind' => 'drawdown', 'principal_amount' => 1000, 'movement_date' => '2026-08-29'])->assertCreated();
    $this->getJson('/api/v1/accounting/debt-and-loans/position')->assertOk()->assertJsonPath('data.outstanding', 1000);
});

it('requires debt write ability', function (): void {
    $user = User::factory()->create();
    $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Read only debt', 'personal_team' => false]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.debt-and-loans.read']);
    $this->postJson('/api/v1/accounting/debt-and-loans/facilities', [])->assertForbidden();
});
