<?php

declare(strict_types=1);

namespace Liberu\Accounting\GoodsAndServiceReceiptsApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Accounting\GoodsAndServiceReceipts\Models\Receipt; /** @mixin Receipt */
final class ReceiptResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $r = $this->resource;

        return ['id' => $r->getKey(), 'receipt_ref' => $r->receipt_ref, 'receipt_type' => $r->receipt_type->value, 'supplier_ref' => $r->supplier_ref, 'purchase_order_ref' => $r->purchase_order_ref, 'currency' => $r->currency, 'status' => $r->status->value, 'inventory_ref' => $r->inventory_ref, 'project_ref' => $r->project_ref, 'total_value' => $r->total_value, 'lines' => $this->whenLoaded('lines'), 'confirmations' => $this->whenLoaded('confirmations'), 'returns' => $this->whenLoaded('returns'), 'attachments' => $this->whenLoaded('attachments'), 'accruals' => $this->whenLoaded('accruals')];
    }
}
