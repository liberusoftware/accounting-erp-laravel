<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\SalesOrders\Models\SalesOrder;
use Liberu\Foundation\Organizations\Models\Team;

uses(RefreshDatabase::class);

function salesOrdersApiUser(): array
{
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.sales-orders.read', 'accounting.sales-orders.write']);

    return [$user, $team];
}

it('scopes sales orders and mutations to the current team', function (): void {
    [, $team] = salesOrdersApiUser();
    $otherTeam = Team::factory()->create();
    $other = SalesOrder::query()->create(['team_id' => $otherTeam->id, 'customer_id' => 'CUS-2', 'order_number' => 'SO-2', 'order_date' => '2026-08-01', 'currency' => 'GBP', 'status' => 'draft']);
    SalesOrder::query()->create(['team_id' => $team->id, 'customer_id' => 'CUS-1', 'order_number' => 'SO-1', 'order_date' => '2026-08-01', 'currency' => 'GBP', 'status' => 'draft']);

    $this->getJson('/api/v1/accounting/sales-orders')->assertOk()->assertJsonCount(1, 'data');
    $this->postJson('/api/v1/accounting/sales-orders/'.$other->id.'/transition', ['status' => 'confirmed'])->assertNotFound();
});

it('uses the authenticated team when creating a sales order', function (): void {
    [, $team] = salesOrdersApiUser();
    $otherTeam = Team::factory()->create();

    $this->postJson('/api/v1/accounting/sales-orders', ['team_id' => $otherTeam->id, 'customer_id' => 'CUS-3', 'currency' => 'GBP', 'order_date' => '2026-08-01', 'items' => [['description' => 'Service', 'quantity' => 2, 'unit_price' => 50]]])
        ->assertCreated()->assertJsonPath('data.attributes.customer_id', 'CUS-3');

    expect(SalesOrder::query()->where('customer_id', 'CUS-3')->value('team_id'))->toBe($team->id);
});
