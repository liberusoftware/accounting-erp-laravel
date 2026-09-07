<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('creates and validates contractor reports through the API', function (): void {
    $user = User::factory()->create();
    $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Contractor API', 'personal_team' => false]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.contractor-reporting.read', 'accounting.contractor-reporting.write']);

    $report = $this->postJson('/api/v1/accounting/contractor-reporting', [
        'payee_ref' => 'api-payee',
        'tax_year' => 2026,
        'classification' => 'nonemployee',
        'threshold' => 600,
        'form_type' => '1099-NEC',
    ])->assertCreated()->json('data');

    $this->postJson("/api/v1/accounting/contractor-reporting/{$report['id']}/validate", [
        'tax_id' => '12-3456789',
        'legal_name' => 'API Contractor',
    ])->assertOk();

    $this->getJson('/api/v1/accounting/contractor-reporting')
        ->assertOk()
        ->assertJsonCount(1, 'data');
});
