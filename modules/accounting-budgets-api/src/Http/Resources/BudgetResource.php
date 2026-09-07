<?php

declare(strict_types=1);

namespace Liberu\Accounting\BudgetsApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class BudgetResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => (string) $this->id,
            'type' => 'accounting-budgets',
            'attributes' => [
                'team_id' => $this->team_id,
                'name' => $this->name,
                'period_start' => $this->period_start?->toDateString(),
                'period_end' => $this->period_end?->toDateString(),
                'currency' => $this->currency,
                'status' => $this->status?->value,
                'version' => $this->version,
                'notes' => $this->notes,
                'metadata' => $this->metadata,
                'lines' => $this->whenLoaded('lines'),
            ],
            'links' => ['self' => url('/api/v1/accounting/budgets/'.$this->id)],
        ];
    }
}
