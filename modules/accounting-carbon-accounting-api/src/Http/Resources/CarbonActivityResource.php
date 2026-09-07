<?php

declare(strict_types=1);

namespace Liberu\Accounting\CarbonAccountingApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class CarbonActivityResource extends JsonResource
{
    public function toArray($request): array
    {
        return ['id' => (string) $this->id, 'type' => 'accounting-carbon-activities', 'attributes' => ['activity_date' => $this->activity_date?->toDateString(), 'scope' => $this->scope, 'category' => $this->category, 'description' => $this->description, 'quantity' => $this->quantity, 'unit' => $this->unit, 'emission_factor' => $this->emission_factor, 'co2e' => $this->co2e, 'factor_source' => $this->factor_source, 'evidence' => $this->evidence, 'is_estimate' => $this->is_estimate]];
    }
}
