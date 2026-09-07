<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\WorkforceCosting\Models\WorkforceCost;
use Liberu\Foundation\Organizations\Models\Team;

uses(RefreshDatabase::class);

it('manages workforce costs and allocations through the API', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.workforce-costing.read', 'accounting.workforce-costing.write']);

    $this->postJson('/api/v1/accounting/workforce-costing/costs', ['worker_ref' => 'employee-1', 'cost_date' => '2026-08-01', 'hours' => 8, 'hourly_rate' => 25])->assertCreated();
    $cost = WorkforceCost::query()->firstOrFail();
    $this->postJson('/api/v1/accounting/workforce-costing/costs/'.$cost->id.'/allocate', ['allocation_type' => 'department', 'allocation_ref' => 'department-2'])->assertOk();
    $this->postJson('/api/v1/accounting/workforce-costing/costs/'.$cost->id.'/capitalize')->assertOk();
    $this->getJson('/api/v1/accounting/workforce-costing/profitability')->assertOk()->assertJsonStructure(['department:department-2']);
});

it('requires workforce costing abilities', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, []);

    $this->getJson('/api/v1/accounting/workforce-costing/costs')->assertForbidden();
    $this->postJson('/api/v1/accounting/workforce-costing/costs', [])->assertForbidden();
});
