<?php

declare(strict_types=1);

namespace Liberu\Accounting\InventoryAccountingApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Accounting\InventoryAccounting\Models\InventoryItem; /** @mixin InventoryItem */
final class InventoryItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $item = $this->resource;

        return ['id' => $item->getKey(), 'item_ref' => $item->item_ref, 'description' => $item->description, 'warehouse_ref' => $item->warehouse_ref, 'currency' => $item->currency, 'valuation_method' => $item->valuation_method->value, 'status' => $item->status->value, 'quantity_on_hand' => $item->quantity_on_hand, 'inventory_value' => $item->inventory_value, 'layers' => $this->whenLoaded('layers'), 'movements' => $this->whenLoaded('movements')];
    }
}
