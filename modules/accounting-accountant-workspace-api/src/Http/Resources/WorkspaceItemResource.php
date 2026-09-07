<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountantWorkspaceApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class WorkspaceItemResource extends JsonResource
{
    public function toArray($request): array
    {
        return ['id' => (string) $this->id, 'type' => 'accounting-workspace-items', 'attributes' => ['name' => $this->name, 'subject_type' => $this->subject_type, 'subject_id' => $this->subject_id, 'status' => $this->status?->value, 'next_deadline' => $this->next_deadline?->toIso8601String(), 'alerts' => $this->alerts, 'notes' => $this->notes, 'requests' => $this->requests, 'metadata' => $this->metadata]];
    }
}
