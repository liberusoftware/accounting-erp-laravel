<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('manages depreciation schedules and runs through the API', function (): void {
    $user = User::factory()->create();
    $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Depreciation API', 'personal_team' => false]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.depreciation.read', 'accounting.depreciation.write']);

    $schedule = $this->postJson('/api/v1/accounting/depreciation/schedules', [
        'asset_ref' => 'api-asset', 'book_ref' => 'statutory', 'method' => 'straight_line', 'useful_life_months' => 24, 'cost' => 2400, 'start_date' => '2026-01-01', 'currency' => 'GBP',
    ])->assertCreated()->json('data');

    $run = $this->postJson('/api/v1/accounting/depreciation/schedules/'.$schedule['id'].'/runs', ['period_start' => '2026-01-01', 'period_end' => '2026-01-31'])->assertCreated()->json('data');
    $this->postJson('/api/v1/accounting/depreciation/runs/'.$run['id'].'/post', ['journal_ref' => 'API-JRN'])->assertOk();
    $this->getJson('/api/v1/accounting/depreciation/forecast')->assertOk()->assertJsonPath('data.0.asset_ref', 'api-asset');
});

it('requires depreciation abilities', function (): void {
    $user = User::factory()->create();
    $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Read only', 'personal_team' => false]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.depreciation.read']);

    $this->postJson('/api/v1/accounting/depreciation/schedules', [])->assertForbidden();
});
