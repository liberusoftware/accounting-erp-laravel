<?php

declare(strict_types=1);

namespace Liberu\Accounting\TaxCoreApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Accounting\TaxCore\Models\TaxRule;

/** @mixin TaxRule */
final class TaxRuleResource extends JsonResource
{
    public function toArray($request): array
    {
        return ['id' => (string) $this->id, 'type' => 'accounting-tax-core', 'attributes' => ['code' => $this->code, 'name' => $this->name, 'tax_type' => $this->tax_type, 'jurisdiction_code' => $this->jurisdiction_code, 'rate' => (float) $this->rate, 'treatment' => $this->treatment->value, 'effective_from' => $this->effective_from->toDateString(), 'effective_until' => $this->effective_until?->toDateString(), 'status' => $this->status->value, 'exemption_code' => $this->exemption_code, 'control_account_code' => $this->control_account_code, 'rounding_method' => $this->rounding_method, 'rounding_scale' => $this->rounding_scale, 'created_at' => $this->created_at->toIso8601String(), 'updated_at' => $this->updated_at->toIso8601String()], 'links' => ['self' => url('/api/v1/accounting/tax-core/'.$this->id)]];
    }
}
