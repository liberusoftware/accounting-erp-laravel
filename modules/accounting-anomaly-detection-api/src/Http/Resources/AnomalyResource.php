<?php

declare(strict_types=1);

namespace Liberu\Accounting\AnomalyDetectionApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class AnomalyResource extends JsonResource
{
    public function toArray($request): array
    {
        return ['id' => (string) $this->id, 'type' => 'accounting-anomalies', 'attributes' => ['kind' => $this->kind, 'source_type' => $this->source_type, 'source_id' => $this->source_id, 'title' => $this->title, 'description' => $this->description, 'confidence' => $this->confidence, 'status' => $this->status?->value, 'evidence' => $this->evidence, 'detected_at' => $this->detected_at?->toIso8601String()]];
    }
}
