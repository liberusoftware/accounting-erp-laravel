<?php

declare(strict_types=1);

namespace Liberu\Accounting\CoreApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class BookResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => (string) $this->resource->getKey(), 'type' => 'accounting-book', 'attributes' => ['legal_entity_id' => (string) $this->resource->legal_entity_id, 'name' => $this->resource->name, 'code' => $this->resource->code, 'accounting_basis' => $this->resource->accounting_basis, 'is_active' => $this->resource->is_active, 'created_at' => $this->resource->created_at?->toISOString(), 'updated_at' => $this->resource->updated_at?->toISOString()]];
    }
}
