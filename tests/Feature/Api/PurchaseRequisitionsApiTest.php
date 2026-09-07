<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\PurchaseRequisitions\Models\PurchaseRequisition;
use Liberu\Foundation\Organizations\Models\Team;

uses(RefreshDatabase::class);

function purchaseRequisitionsApiUser(): array
{
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.purchase-requisitions.read', 'accounting.purchase-requisitions.write']);

    return [$user, $team];
}

it('scopes requisitions and workflow mutations to the current team', function (): void {
    [, $team] = purchaseRequisitionsApiUser();
    $otherTeam = Team::factory()->create();
    $other = PurchaseRequisition::query()->create(['team_id' => $otherTeam->id, 'requester_ref' => 'USER-2', 'currency' => 'GBP', 'total_amount' => 100, 'lines' => [['item_ref' => 'ITEM-2']], 'status' => 'draft']);
    PurchaseRequisition::query()->create(['team_id' => $team->id, 'requester_ref' => 'USER-1', 'currency' => 'GBP', 'total_amount' => 100, 'lines' => [['item_ref' => 'ITEM-1']], 'status' => 'draft']);

    $this->getJson('/api/v1/accounting/purchase-requisitions')->assertOk()->assertJsonCount(1, 'data');
    $this->postJson('/api/v1/accounting/purchase-requisitions/'.$other->id.'/transition', ['status' => 'cancelled'])->assertNotFound();
});

it('uses the authenticated team when creating a requisition', function (): void {
    [, $team] = purchaseRequisitionsApiUser();
    $otherTeam = Team::factory()->create();

    $this->postJson('/api/v1/accounting/purchase-requisitions', ['team_id' => $otherTeam->id, 'requester_ref' => 'USER-3', 'currency' => 'GBP', 'total_amount' => 100, 'lines' => [['item_ref' => 'ITEM-3']]])
        ->assertCreated()->assertJsonPath('data.requester_ref', 'USER-3');

    expect(PurchaseRequisition::query()->where('requester_ref', 'USER-3')->value('team_id'))->toBe($team->id);
});
