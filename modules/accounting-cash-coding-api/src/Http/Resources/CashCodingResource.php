<?php

declare(strict_types=1);

namespace Liberu\Accounting\CashCodingApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Accounting\CashCoding\Models\CashCodingBatch;

/** @mixin CashCodingBatch */
final class CashCodingResource extends JsonResource
{
    public function toArray($request): array
    {
        return ['id' => (string) $this->id, 'type' => 'accounting-cash-coding', 'attributes' => ['reference' => $this->reference, 'status' => $this->status?->value, 'lines' => $this->lines, 'payee_creation_policy' => $this->payee_creation_policy, 'total_amount' => (float) $this->total_amount, 'currency' => $this->currency, 'reviewed_by' => $this->reviewed_by, 'posted_by' => $this->posted_by, 'posted_at' => $this->posted_at?->toIso8601String(), 'undone_at' => $this->undone_at?->toIso8601String()]];
    }
}
