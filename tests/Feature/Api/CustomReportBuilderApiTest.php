<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('creates custom report definitions and export requests through the API', function (): void {
    $user = User::factory()->create();
    $team = Team::forceCreate(['user_id' => $user->id, 'name' => 'Reports API', 'personal_team' => false]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.custom-report-builder.read', 'accounting.custom-report-builder.write']);
    $report = $this->postJson('/api/v1/accounting/custom-report-builder', ['report_ref' => 'API-R', 'name' => 'API report', 'measures' => ['balance']])->assertCreated()->json('data');
    $this->postJson("/api/v1/accounting/custom-report-builder/{$report['id']}/exports", ['format' => 'csv'])->assertCreated();
    $this->getJson('/api/v1/accounting/custom-report-builder')->assertOk()->assertJsonCount(1, 'data');
});
