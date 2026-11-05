<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\Erp\Models\Team;
use Liberu\Accounting\Erp\Models\User;

uses(RefreshDatabase::class);

it('creates dashboards and KPIs through the API', function (): void {
    $user = User::factory()->create();
    $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Dashboard API', 'personal_team' => false]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.dashboards.read', 'accounting.dashboards.write']);
    $dashboard = $this->postJson('/api/v1/accounting/dashboards', ['dashboard_ref' => 'API-DASH', 'name' => 'API dashboard'])->assertCreated()->json('data');
    $this->postJson("/api/v1/accounting/dashboards/{$dashboard['id']}/kpis", ['kpi_ref' => 'cash', 'label' => 'Cash', 'value' => 500])->assertCreated();
    $this->getJson('/api/v1/accounting/dashboards')->assertOk()->assertJsonCount(1, 'data');
});
