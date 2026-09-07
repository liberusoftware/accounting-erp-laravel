<?php

declare(strict_types=1);
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);
it('records carbon activity and calculates co2e for the current team', function (): void {
    $user = User::factory()->create();
    $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Carbon Team', 'personal_team' => false]);
    $user->teams()->attach($team, ['role' => 'admin']);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.carbon-accounting.read', 'accounting.carbon-accounting.write']);
    $this->postJson('/api/v1/accounting/carbon/activities', ['activity_date' => '2026-01-01', 'scope' => '2', 'category' => 'electricity', 'description' => 'January electricity', 'quantity' => 100, 'unit' => 'kWh', 'emission_factor' => '0.233'])->assertCreated()->assertJsonPath('data.attributes.co2e', '23.30000000');
});
