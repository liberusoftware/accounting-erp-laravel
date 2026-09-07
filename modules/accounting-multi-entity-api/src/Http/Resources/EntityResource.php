<?php

declare(strict_types=1);

namespace Liberu\Accounting\MultiEntityApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class EntityResource extends JsonResource
{
    public function toArray($request): array
    {
        return ['id' => (string) $this->resource->getKey(), 'type' => 'accounting-multi-entity', 'attributes' => ['team_id' => $this->resource->team_id, 'entity_ref' => $this->resource->entity_ref, 'code' => $this->resource->code, 'name' => $this->resource->name, 'base_currency' => $this->resource->base_currency, 'timezone' => $this->resource->timezone, 'status' => $this->resource->status->value], 'links' => ['self' => url('/api/v1/accounting/multi-entity/'.$this->resource->getKey())]];
    }
}
