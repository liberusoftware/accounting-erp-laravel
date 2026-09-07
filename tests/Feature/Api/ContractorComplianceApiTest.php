<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('registers and lists contractors through the API', function (): void {
    $user = User::factory()->create();
    $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Compliance API', 'personal_team' => false]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.contractor-compliance.read', 'accounting.contractor-compliance.write']);

    $this->postJson('/api/v1/accounting/contractor-compliance', ['contractor_ref' => 'api-contractor', 'legal_name' => 'API Contractor', 'classification' => 'cis'])
        ->assertCreated();

    $this->getJson('/api/v1/accounting/contractor-compliance')->assertOk()->assertJsonCount(1, 'data');
});
