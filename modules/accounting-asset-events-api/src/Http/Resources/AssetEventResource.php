<?php

declare(strict_types=1);

namespace Liberu\Accounting\AssetEventsApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class AssetEventResource extends JsonResource
{
    public function toArray($request): array
    {
        return ['id' => (string) $this->id, 'type' => 'accounting-asset-events', 'attributes' => ['asset_id' => $this->asset_id, 'event_type' => $this->event_type, 'event_date' => $this->event_date?->toDateString(), 'description' => $this->description, 'source_type' => $this->source_type, 'source_id' => $this->source_id, 'old_values' => $this->old_values, 'new_values' => $this->new_values, 'evidence' => $this->evidence, 'actor_id' => $this->actor_id]];
    }
}
