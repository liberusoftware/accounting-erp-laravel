<?php

declare(strict_types=1);

namespace Liberu\Accounting\CoreApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class NumberingSequenceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => (string) $this->resource->getKey(), 'type' => 'accounting-numbering-sequence', 'attributes' => ['book_id' => (string) $this->resource->book_id, 'key' => $this->resource->key, 'prefix' => $this->resource->prefix, 'next_number' => $this->resource->next_number, 'padding' => $this->resource->padding]];
    }
}
