<?php

declare(strict_types=1);

namespace Liberu\Accounting\AuditSupportApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class AuditRequestResource extends JsonResource
{
    public function toArray($request): array
    {
        return ['id' => (string) $this->id, 'type' => 'accounting-audit-requests', 'attributes' => ['reference' => $this->reference, 'title' => $this->title, 'description' => $this->description, 'owner_id' => $this->owner_id, 'status' => $this->status?->value, 'due_at' => $this->due_at?->toIso8601String(), 'evidence' => $this->evidence]];
    }
}
