<?php

declare(strict_types=1);

namespace Liberu\Accounting\EstimatesAndQuotesApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Accounting\EstimatesAndQuotes\Models\Estimate;

/** @mixin Estimate */ final class EstimateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $e = $this->resource;

        return ['id' => $e->getKey(), 'quote_ref' => $e->quote_ref, 'customer_ref' => $e->customer_ref, 'name' => $e->name, 'currency' => $e->currency, 'status' => $e->status->value, 'issue_date' => $e->issue_date?->toDateString(), 'expires_on' => $e->expires_on?->toDateString(), 'version' => $e->version, 'terms' => $e->terms, 'brand' => $e->brand, 'converted_ref' => $e->converted_ref, 'total' => (float) $e->items->sum('amount'), 'items' => $this->whenLoaded('items'), 'versions' => $this->whenLoaded('versions'), 'history' => $this->whenLoaded('history')];
    }
}
