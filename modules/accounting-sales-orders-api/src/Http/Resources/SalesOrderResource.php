<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesOrdersApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Accounting\SalesOrders\Models\SalesOrder;

/** @mixin SalesOrder */
final class SalesOrderResource extends JsonResource
{
    public function toArray($request): array
    {
        return ['id' => (string) $this->id, 'type' => 'accounting-sales-orders', 'attributes' => ['customer_id' => $this->customer_id, 'estimate_id' => $this->estimate_id, 'order_number' => $this->order_number, 'status' => $this->status->value, 'currency' => $this->currency, 'subtotal' => (float) $this->subtotal, 'tax_total' => (float) $this->tax_total, 'total' => (float) $this->total, 'invoiced_total' => (float) $this->invoiced_total, 'items' => $this->when($this->relationLoaded('items'), fn () => $this->items->map(fn ($item) => ['id' => (string) $item->id, 'description' => $item->description, 'quantity' => (float) $item->quantity, 'unit_price' => (float) $item->unit_price, 'amount' => (float) $item->amount, 'tax_amount' => (float) $item->tax_amount])->values()->all())], 'links' => ['self' => url('/api/v1/accounting/sales-orders/'.$this->id)]];
    }
}
