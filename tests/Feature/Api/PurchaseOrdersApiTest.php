<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\PurchaseOrders\Models\PurchaseOrder;
use Liberu\Foundation\Organizations\Models\Team;

uses(RefreshDatabase::class);

function purchaseOrdersApiUser(): array
{
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.purchase-orders.read', 'accounting.purchase-orders.write']);

    return [$user, $team];
}

it('scopes purchase orders and mutations to the current team', function (): void {
    [, $team] = purchaseOrdersApiUser();
    $otherTeam = Team::factory()->create();
    $other = PurchaseOrder::query()->create(['team_id' => $otherTeam->id, 'supplier_ref' => 'SUP-2', 'order_number' => 'PO-2', 'currency' => 'GBP', 'order_date' => '2026-08-01', 'total_amount' => 100, 'status' => 'draft']);
    PurchaseOrder::query()->create(['team_id' => $team->id, 'supplier_ref' => 'SUP-1', 'order_number' => 'PO-1', 'currency' => 'GBP', 'order_date' => '2026-08-01', 'total_amount' => 100, 'status' => 'draft']);

    $this->getJson('/api/v1/accounting/purchase-orders')->assertOk()->assertJsonCount(1, 'data');
    $this->postJson('/api/v1/accounting/purchase-orders/'.$other->id.'/transition', ['status' => 'cancelled'])->assertNotFound();
});

it('uses the authenticated team when creating an order', function (): void {
    [, $team] = purchaseOrdersApiUser();
    $otherTeam = Team::factory()->create();

    $this->postJson('/api/v1/accounting/purchase-orders', ['team_id' => $otherTeam->id, 'supplier_ref' => 'SUP-3', 'currency' => 'GBP', 'lines' => [['item_ref' => 'ITEM-1', 'quantity' => 2, 'unit_price' => 50]]])
        ->assertCreated()->assertJsonPath('data.order_number', fn ($value): bool => is_string($value) && str_starts_with($value, 'PO-'));

    expect(PurchaseOrder::query()->where('supplier_ref', 'SUP-3')->value('team_id'))->toBe($team->id);
});
