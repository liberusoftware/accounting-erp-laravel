<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\RecurringTransactions\Models\RecurringTemplate;
use Liberu\Foundation\Organizations\Models\Team;

uses(RefreshDatabase::class);

it('scopes recurring templates and ignores supplied team ids', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $otherTeam = Team::factory()->create();
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.recurring-transactions.read', 'accounting.recurring-transactions.write']);
    $other = RecurringTemplate::query()->create(['team_id' => $otherTeam->id, 'name' => 'Other', 'transaction_type' => 'journal', 'frequency' => 'monthly', 'starts_on' => '2026-01-01', 'next_run_on' => '2026-01-01', 'payload' => []]);

    $this->getJson('/api/v1/accounting/recurring-transactions')->assertOk()->assertJsonCount(0, 'data');
    $this->getJson('/api/v1/accounting/recurring-transactions/'.$other->id)->assertNotFound();
    $this->postJson('/api/v1/accounting/recurring-transactions', ['team_id' => $otherTeam->id, 'name' => 'Current', 'transaction_type' => 'journal', 'frequency' => 'monthly', 'starts_on' => '2026-01-01', 'payload' => ['amount' => 1]])->assertCreated();

    expect(RecurringTemplate::query()->where('name', 'Current')->value('team_id'))->toBe($team->id);
});

it('requires the recurring transactions write ability', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.recurring-transactions.read']);

    $this->postJson('/api/v1/accounting/recurring-transactions', ['name' => 'Read-only', 'transaction_type' => 'journal', 'frequency' => 'monthly', 'starts_on' => '2026-01-01', 'payload' => []])->assertForbidden();
});
