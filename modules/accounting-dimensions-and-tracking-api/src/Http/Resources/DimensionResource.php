<?php

declare(strict_types=1);

namespace Liberu\Accounting\DimensionsApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Accounting\Dimensions\Models\Dimension;
use Liberu\Accounting\Dimensions\Models\DimensionValue;

final class DimensionResource extends JsonResource
{
    public function toArray($request): array
    {
        $dimension = $this->resource;
        if (! $dimension instanceof Dimension) {
            return [];
        }

        return ['id' => (string) $dimension->id, 'type' => 'accounting-dimension', 'attributes' => ['code' => $dimension->code, 'name' => $dimension->name, 'kind' => $dimension->kind->value, 'description' => $dimension->description, 'is_required' => $dimension->is_required, 'is_active' => $dimension->is_active, 'values' => $this->whenLoaded('values', fn () => $dimension->values->map(fn (DimensionValue $value): array => ['id' => (string) $value->id, 'code' => $value->code, 'name' => $value->name, 'is_active' => $value->is_active])->values())]];
    }
}
