<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('creates and verifies construction tax records through the API', function (): void {
    $user = User::factory()->create();
    $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Construction Tax API', 'personal_team' => false]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.construction-tax.read', 'accounting.construction-tax.write']);

    $record = $this->postJson('/api/v1/accounting/construction-tax', ['subcontractor_ref' => 'api-sub', 'tax_period' => '2026-08', 'deduction_rate' => 20, 'gross_amount' => 500])->assertCreated()->json('data');
    $this->postJson("/api/v1/accounting/construction-tax/{$record['id']}/verify", ['reference' => 'api-verify'])->assertOk();
    $this->getJson('/api/v1/accounting/construction-tax')->assertOk()->assertJsonCount(1, 'data');
});
