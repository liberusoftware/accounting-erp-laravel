<?php

declare(strict_types=1);

namespace Liberu\Accounting\ReviewApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class ReviewItemResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => (string) $this->id,
            'type' => 'accounting-review-items',
            'attributes' => [
                'team_id' => $this->team_id,
                'item_type' => $this->item_type,
                'source_type' => $this->source_type,
                'source_id' => $this->source_id,
                'severity' => $this->severity,
                'status' => $this->status?->value,
                'title' => $this->title,
                'details' => $this->details,
                'resolution' => $this->resolution,
                'signoff' => $this->signoff,
                'due_at' => $this->due_at?->toIso8601String(),
            ],
            'links' => ['self' => url('/api/v1/accounting/review/'.$this->id)],
        ];
    }
}
