<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\TimeTracking\Models\TimeEntry;
use Liberu\Foundation\Organizations\Models\Team;

uses(RefreshDatabase::class);

it('scopes time entries and supports approval through the API', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.time-tracking.read', 'accounting.time-tracking.write', 'accounting.time-tracking.approve']);

    $this->postJson('/api/v1/accounting/time-tracking/entries', ['worker_ref' => 'worker-1', 'worked_on' => '2026-08-29', 'hours' => 7.5])->assertCreated();
    $entry = TimeEntry::query()->firstOrFail();
    $this->getJson('/api/v1/accounting/time-tracking/entries')->assertOk()->assertJsonCount(1, 'data');
    $this->postJson('/api/v1/accounting/time-tracking/entries/'.$entry->id.'/submit')->assertOk();
    $this->postJson('/api/v1/accounting/time-tracking/entries/'.$entry->id.'/approve')->assertOk();
});

it('requires time tracking abilities', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, []);

    $this->getJson('/api/v1/accounting/time-tracking/entries')->assertForbidden();
    $this->postJson('/api/v1/accounting/time-tracking/entries', [])->assertForbidden();
});
