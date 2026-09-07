<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\ProductAndServiceItems\Actions\SaveAccountingItem;
use Liberu\Accounting\ProductAndServiceItems\Enums\ItemKind;
use Liberu\Accounting\ProductAndServiceItems\Exceptions\InvalidItem;
use Liberu\Accounting\ProductAndServiceItems\Models\AccountingItem;
use Liberu\Accounting\ProductAndServiceItems\Queries\FindAccountingItems;

uses(RefreshDatabase::class);
it('saves item and service master data idempotently with pricing and references', function (): void {
    $action = app(SaveAccountingItem::class);
    $attributes = ['team_id' => 1, 'code' => 'SKU-1', 'name' => 'Consulting', 'kind' => 'service', 'sales_description' => 'Professional services', 'purchase_description' => 'External delivery', 'sales_account_ref' => '4000', 'purchase_account_ref' => '5000', 'tax_default_ref' => 'VAT20', 'unit' => 'hour', 'sales_price' => 150, 'purchase_price' => 75, 'currency' => 'GBP', 'ecommerce_refs' => ['shop' => 'item-1']];
    $item = $action->handle($attributes);
    $same = $action->handle(array_merge($attributes, ['sales_price' => 175]));
    expect($same->id)->toBe($item->id)->and(AccountingItem::count())->toBe(1)->and(app(FindAccountingItems::class)->search('Consult')->first()->kind)->toBe(ItemKind::Service);
});
it('rejects missing identity and negative prices', function (): void {
    $action = app(SaveAccountingItem::class);
    expect(fn () => $action->handle(['team_id' => 1, 'name' => 'Missing code']))->toThrow(InvalidItem::class);
    expect(fn () => $action->handle(['team_id' => 1, 'code' => 'X', 'name' => 'Bad', 'purchase_price' => -1]))->toThrow(InvalidItem::class);
});
