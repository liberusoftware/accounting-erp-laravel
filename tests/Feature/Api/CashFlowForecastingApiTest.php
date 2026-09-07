<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('creates and lists cash-flow forecasts through the API', function (): void {
    $user = User::factory()->create();
    $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Cash Flow API', 'personal_team' => false]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.cash-flow-forecasting.read', 'accounting.cash-flow-forecasting.write']);

    $this->postJson('/api/v1/accounting/cash-flow-forecasting', ['forecast_ref' => 'api-forecast', 'currency' => 'USD', 'starts_on' => '2026-09-01', 'ends_on' => '2026-12-31'])->assertCreated();
    $this->getJson('/api/v1/accounting/cash-flow-forecasting')->assertOk()->assertJsonCount(1, 'data');
});
