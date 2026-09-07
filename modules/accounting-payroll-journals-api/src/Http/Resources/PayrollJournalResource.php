<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollJournalsApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class PayrollJournalResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->resource->getKey(),
            'type' => 'accounting-payroll-journal',
            'attributes' => [
                'team_id' => (int) $this->resource->team_id,
                'journal_ref' => $this->resource->journal_ref,
                'payroll_period_start' => $this->resource->payroll_period_start?->toDateString(),
                'payroll_period_end' => $this->resource->payroll_period_end?->toDateString(),
                'currency' => $this->resource->currency,
                'gross_pay' => (string) $this->resource->gross_pay,
                'taxes' => (string) $this->resource->taxes,
                'deductions' => (string) $this->resource->deductions,
                'benefits' => (string) $this->resource->benefits,
                'employer_costs' => (string) $this->resource->employer_costs,
                'net_pay' => (string) $this->resource->net_pay,
                'liabilities' => $this->resource->liabilities,
                'allocation' => $this->resource->allocation,
                'status' => $this->resource->status?->value,
                'posted_at' => $this->resource->posted_at?->toISOString(),
                'reversed_at' => $this->resource->reversed_at?->toISOString(),
                'reversal_ref' => $this->resource->reversal_ref,
                'correction_ref' => $this->resource->correction_ref,
                'metadata' => $this->resource->metadata,
            ],
        ];
    }
}
