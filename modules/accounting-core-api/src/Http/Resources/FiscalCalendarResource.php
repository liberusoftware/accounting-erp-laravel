<?php

declare(strict_types=1);

namespace Liberu\Accounting\CoreApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class FiscalCalendarResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => (string) $this->resource->getKey(), 'type' => 'accounting-fiscal-calendar', 'attributes' => ['book_id' => (string) $this->resource->book_id, 'starts_on' => $this->resource->starts_on?->toDateString(), 'ends_on' => $this->resource->ends_on?->toDateString(), 'is_closed' => $this->resource->is_closed]];
    }
}
