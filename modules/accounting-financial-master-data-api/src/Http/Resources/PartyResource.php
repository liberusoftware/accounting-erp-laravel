<?php

declare(strict_types=1);

namespace Liberu\Accounting\FinancialMasterDataApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Accounting\FinancialMasterData\Models\Party;

/** @mixin Party */
final class PartyResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return ['id' => (string) $this->resource->getKey(), 'type' => 'accounting-financial-master-data-party', 'attributes' => [
            'legal_entity_id' => $this->resource->legal_entity_id, 'type' => $this->resource->type?->value, 'reference' => $this->resource->reference,
            'name' => $this->resource->name, 'email' => $this->resource->email, 'phone' => $this->resource->phone, 'tax_identifier' => $this->resource->tax_identifier,
            'payment_term_id' => $this->resource->payment_term_id, 'credit_limit' => $this->resource->credit_limit, 'status' => $this->resource->status?->value,
            'metadata' => $this->resource->metadata, 'created_at' => $this->resource->created_at?->toISOString(), 'updated_at' => $this->resource->updated_at?->toISOString(),
        ]];
    }
}
