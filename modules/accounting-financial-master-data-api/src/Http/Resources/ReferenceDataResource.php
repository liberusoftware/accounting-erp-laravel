<?php

declare(strict_types=1);

namespace Liberu\Accounting\FinancialMasterDataApi\Http\Resources;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Model */
final class ReferenceDataResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        $attributes = $this->resource->getAttributes();
        unset($attributes['id'], $attributes['created_at'], $attributes['updated_at']);
        foreach (['status', 'type'] as $enum) {
            if (isset($this->resource->{$enum}) && is_object($this->resource->{$enum})) {
                $attributes[$enum] = $this->resource->{$enum}->value;
            }
        }

        return ['id' => (string) $this->resource->getKey(), 'type' => 'accounting-financial-master-data-'.str_replace('_', '-', $this->resource->getTable()), 'attributes' => $attributes];
    }
}
