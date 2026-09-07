<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProductAndServiceItemsApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class AccountingItemResource extends JsonResource
{
    /** @return array<string,mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'kind' => $this->kind?->value,
            'purchase_description' => $this->purchase_description,
            'sales_description' => $this->sales_description,
            'sales_account_ref' => $this->sales_account_ref,
            'purchase_account_ref' => $this->purchase_account_ref,
            'tax_default_ref' => $this->tax_default_ref,
            'unit' => $this->unit,
            'purchase_price' => $this->purchase_price,
            'sales_price' => $this->sales_price,
            'currency' => $this->currency,
            'status' => $this->status?->value,
            'ecommerce_refs' => $this->ecommerce_refs,
            'metadata' => $this->metadata,
        ];
    }
}
