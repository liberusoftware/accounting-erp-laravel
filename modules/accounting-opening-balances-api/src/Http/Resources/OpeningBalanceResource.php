<?php

declare(strict_types=1);

namespace Liberu\Accounting\OpeningBalancesApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class OpeningBalanceResource extends JsonResource
{
    public function toArray($request): array
    {
        return ['id' => (string) $this->resource->getKey(), 'type' => 'accounting-opening-balances', 'attributes' => ['team_id' => $this->resource->team_id, 'batch_ref' => $this->resource->batch_ref, 'migration_date' => $this->resource->migration_date?->toDateString(), 'currency' => $this->resource->currency, 'status' => $this->resource->status->value, 'summary' => $this->resource->summary, 'approved_at' => $this->resource->approved_at?->toISOString()], 'links' => ['self' => url('/api/v1/accounting/opening-balances/'.$this->resource->getKey())]];
    }
}
