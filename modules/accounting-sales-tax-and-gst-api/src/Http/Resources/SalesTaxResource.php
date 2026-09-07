<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesTaxAndGstApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Accounting\SalesTaxAndGst\Models\SalesTaxRecord;

/** @mixin SalesTaxRecord */
final class SalesTaxResource extends JsonResource
{
    public function toArray($request): array
    {
        return ['id' => (string) $this->id, 'type' => 'accounting-sales-tax-and-gst', 'attributes' => ['context_id' => $this->context_id, 'record_type' => $this->type->value, 'jurisdiction' => $this->jurisdiction, 'origin' => $this->origin, 'destination' => $this->destination, 'rate' => (float) $this->rate, 'taxable_base' => (float) $this->taxable_base, 'liability' => (float) $this->liability, 'status' => $this->status->value, 'period_start' => $this->period_start, 'period_end' => $this->period_end], 'links' => ['self' => url('/api/v1/accounting/sales-tax-and-gst/'.$this->id)]];
    }
}
