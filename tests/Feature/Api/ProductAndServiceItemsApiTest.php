<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Liberu\Accounting\ProductAndServiceItems\Models\AccountingItem;
use Liberu\Foundation\Organizations\Models\Team;

uses(RefreshDatabase::class);

function productItemsApiUser(): array
{
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->forceFill(['current_team_id' => $team->id])->save();
    Sanctum::actingAs($user, ['accounting.product-and-service-items.read', 'accounting.product-and-service-items.write']);

    return [$user, $team];
}

it('only lists and shows items belonging to the current team', function (): void {
    [, $team] = productItemsApiUser();
    $otherTeam = Team::factory()->create();
    AccountingItem::query()->create(['team_id' => $team->id, 'code' => 'ITEM-1', 'name' => 'Current item', 'kind' => 'item', 'currency' => 'GBP', 'status' => 'active']);
    $other = AccountingItem::query()->create(['team_id' => $otherTeam->id, 'code' => 'ITEM-2', 'name' => 'Other item', 'kind' => 'item', 'currency' => 'GBP', 'status' => 'active']);

    $this->getJson('/api/v1/accounting/product-and-service-items')->assertOk()->assertJsonCount(1, 'data');
    $this->getJson('/api/v1/accounting/product-and-service-items/'.$other->id)->assertNotFound();
});

it('uses the authenticated team when creating an item', function (): void {
    [, $team] = productItemsApiUser();
    $otherTeam = Team::factory()->create();

    $this->postJson('/api/v1/accounting/product-and-service-items', ['team_id' => $otherTeam->id, 'code' => 'SERV-1', 'name' => 'Consulting', 'kind' => 'service', 'currency' => 'GBP', 'sales_price' => 100])
        ->assertCreated()->assertJsonPath('data.code', 'SERV-1');

    expect(AccountingItem::query()->where('code', 'SERV-1')->value('team_id'))->toBe($team->id);
});
