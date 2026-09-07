<?php

declare(strict_types=1);

namespace Liberu\Accounting\MultiCurrencyApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class RevaluationResource extends JsonResource
{
    public function toArray($request): array
    {
        return ['id' => (string) $this->resource->getKey(), 'type' => 'accounting-multi-currency-revaluation', 'attributes' => ['run_ref' => $this->resource->run_ref, 'scope_ref' => $this->resource->scope_ref, 'as_of_date' => $this->resource->as_of_date?->toDateString(), 'functional_currency' => $this->resource->functional_currency, 'status' => $this->resource->status->value, 'realized_gain' => (float) $this->resource->realized_gain, 'realized_loss' => (float) $this->resource->realized_loss, 'unrealized_gain' => (float) $this->resource->unrealized_gain, 'unrealized_loss' => (float) $this->resource->unrealized_loss, 'summary' => $this->resource->summary]];
    }
}
