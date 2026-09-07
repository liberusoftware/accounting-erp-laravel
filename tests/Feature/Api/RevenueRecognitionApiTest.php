<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\RevenueRecognition\Actions\CreateRevenueSchedule;
use Liberu\Accounting\RevenueRecognition\Models\RevenueSchedule;
use Liberu\Foundation\Organizations\Models\Team;

uses(RefreshDatabase::class);

it('scopes revenue schedules through their team-owned obligations', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $otherTeam = Team::factory()->create();
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.revenue-recognition.read', 'accounting.revenue-recognition.write']);
    $other = app(CreateRevenueSchedule::class)->handle(['team_id' => $otherTeam->id, 'currency' => 'GBP', 'total_amount' => 100, 'start_date' => '2026-01-01', 'periods' => 2, 'deferred_account_ref' => 'deferred-other', 'revenue_account_ref' => 'revenue-other']);

    $this->getJson('/api/v1/accounting/revenue-recognition')->assertOk()->assertJsonCount(0, 'data');
    $this->getJson('/api/v1/accounting/revenue-recognition/'.$other->id)->assertNotFound();
    $this->postJson('/api/v1/accounting/revenue-recognition', ['team_id' => $otherTeam->id, 'currency' => 'GBP', 'total_amount' => 50, 'start_date' => '2026-02-01', 'periods' => 1, 'deferred_account_ref' => 'deferred-current', 'revenue_account_ref' => 'revenue-current'])->assertCreated();

    expect(RevenueSchedule::query()->whereHas('obligation', fn ($query) => $query->where('team_id', $team->id))->count())->toBe(1);
});

it('requires the revenue recognition write ability', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.revenue-recognition.read']);

    $this->postJson('/api/v1/accounting/revenue-recognition', ['currency' => 'GBP', 'total_amount' => 50, 'start_date' => '2026-02-01', 'periods' => 1, 'deferred_account_ref' => 'deferred', 'revenue_account_ref' => 'revenue'])->assertForbidden();
});
