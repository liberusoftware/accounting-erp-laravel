<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollIntegrationApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class PayrollImportResource extends JsonResource
{
    /** @return array<string,mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'provider' => $this->provider,
            'period_start' => $this->period_start?->toDateString(),
            'period_end' => $this->period_end?->toDateString(),
            'run_ref' => $this->run_ref,
            'currency' => $this->currency,
            'employee_refs' => $this->employee_refs,
            'contractor_refs' => $this->contractor_refs,
            'dimensions' => $this->dimensions,
            'project_refs' => $this->project_refs,
            'payload_hash' => $this->payload_hash,
            'validation_errors' => $this->validation_errors,
            'adapter_ref' => $this->adapter_ref,
            'status' => $this->status?->value,
            'imported_at' => $this->imported_at?->toISOString(),
            'reconciled_at' => $this->reconciled_at?->toISOString(),
            'metadata' => $this->metadata,
        ];
    }
}
