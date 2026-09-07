<?php

declare(strict_types=1);

namespace Liberu\Accounting\CoreApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class AccountingSettingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $type = $this->resource->getTable() === 'accounting_defaults'
            ? 'accounting-default'
            : 'accounting-policy';

        return [
            'id' => (string) $this->resource->getKey(),
            'type' => $type,
            'attributes' => [
                'book_id' => (string) $this->resource->book_id,
                'key' => $this->resource->key,
                'value' => $this->resource->value,
                'created_at' => $this->resource->created_at?->toISOString(),
                'updated_at' => $this->resource->updated_at?->toISOString(),
            ],
        ];
    }
}
