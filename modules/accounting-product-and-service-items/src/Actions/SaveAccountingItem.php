<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProductAndServiceItems\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\ProductAndServiceItems\Enums\ItemKind;
use Liberu\Accounting\ProductAndServiceItems\Enums\ItemStatus;
use Liberu\Accounting\ProductAndServiceItems\Exceptions\InvalidItem;
use Liberu\Accounting\ProductAndServiceItems\Models\AccountingItem;

final class SaveAccountingItem
{
    /** @param array<string,mixed> $attributes */
    public function handle(array $attributes): AccountingItem
    {
        $code = trim((string) ($attributes['code'] ?? ''));
        $name = trim((string) ($attributes['name'] ?? ''));
        if ($code === '' || $name === '') {
            throw new InvalidItem('Code and name are required.');
        }$kind = ItemKind::from((string) ($attributes['kind'] ?? 'service'));
        foreach (['purchase_price', 'sales_price'] as $field) {
            if (isset($attributes[$field]) && (float) $attributes[$field] < 0) {
                throw new InvalidItem($field.' cannot be negative.');
            }
        }

        return DB::transaction(function () use ($attributes, $code, $kind): AccountingItem {
            $item = AccountingItem::query()->where('team_id', $attributes['team_id'] ?? null)->where('code', $code)->first() ?? new AccountingItem();
            $item->fill(array_merge($attributes, ['code' => $code, 'name' => trim((string) $attributes['name']), 'kind' => $kind, 'status' => $attributes['status'] ?? ItemStatus::Active]));
            $item->save();

            return $item;
        });
    }
}
