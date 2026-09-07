<?php

declare(strict_types=1);

namespace Liberu\Accounting\MatchingIntelligenceApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Accounting\MatchingIntelligence\Models\MatchingSuggestion; /** @mixin MatchingSuggestion */
final class MatchingSuggestionResource extends JsonResource
{
    public function toArray($request): array
    {
        return ['id' => (string) $this->resource->getKey(), 'type' => 'accounting-matching-suggestion', 'attributes' => ['suggestion_ref' => $this->resource->suggestion_ref, 'source_type' => $this->resource->source_type, 'source_id' => $this->resource->source_id, 'target_type' => $this->resource->target_type, 'target_id' => $this->resource->target_id, 'match_type' => $this->resource->match_type, 'confidence' => (float) $this->resource->confidence, 'status' => $this->resource->status?->value, 'explanation' => $this->resource->explanation, 'algorithm_version' => $this->resource->algorithm_version, 'created_at' => $this->resource->created_at?->toISOString()], 'relationships' => ['evidence' => $this->whenLoaded('evidence')]];
    }
}
