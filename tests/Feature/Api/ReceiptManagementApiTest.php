<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\ReceiptManagement\Models\Receipt;
use Liberu\Foundation\Organizations\Models\Team;

uses(RefreshDatabase::class);

it('isolates receipt reads and ignores a supplied team id', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $otherTeam = Team::factory()->create();
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.receipt-management.read', 'accounting.receipt-management.write']);
    $other = Receipt::query()->create(['team_id' => $otherTeam->id, 'file_ref' => 'other.pdf']);

    $this->getJson('/api/v1/accounting/receipt-management')->assertOk()->assertJsonCount(0, 'data');
    $this->getJson('/api/v1/accounting/receipt-management/'.$other->id)->assertNotFound();
    $this->postJson('/api/v1/accounting/receipt-management', ['team_id' => $otherTeam->id, 'file_ref' => 'current.pdf'])->assertCreated();

    expect(Receipt::query()->where('file_ref', 'current.pdf')->value('team_id'))->toBe($team->id);
});

it('requires the receipt write ability', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.receipt-management.read']);

    $this->postJson('/api/v1/accounting/receipt-management', ['file_ref' => 'read-only.pdf'])->assertForbidden();
});
