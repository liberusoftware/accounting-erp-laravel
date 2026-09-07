<?php

declare(strict_types=1);

namespace Liberu\Accounting\InventoryAccounting\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\Accounting\InventoryAccounting\Enums\InventoryStatus;
use Liberu\Accounting\InventoryAccounting\Models\InventoryItem;

final class InventoryQuery
{
    public function paginate(?int $teamId = null, ?InventoryStatus $status = null, int $perPage = 25): LengthAwarePaginator
    {
        return InventoryItem::query()->when($teamId !== null, fn ($q) => $q->where('team_id', $teamId))->when($status !== null, fn ($q) => $q->where('status', $status))->with(['layers', 'movements', 'adjustments', 'landedCosts', 'writeDowns'])->latest()->paginate(min(max($perPage, 1), 100));
    }

    public function valuation(InventoryItem $item): array
    {
        return ['item_ref' => $item->item_ref, 'quantity_on_hand' => (float) $item->quantity_on_hand, 'inventory_value' => (float) $item->inventory_value, 'average_unit_cost' => (float) $item->quantity_on_hand > 0 ? round((float) $item->inventory_value / (float) $item->quantity_on_hand, 4) : 0.0, 'valuation_method' => $item->valuation_method->value];
    }
}
