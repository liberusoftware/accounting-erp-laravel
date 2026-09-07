<?php

declare(strict_types=1);

namespace Liberu\Accounting\ChartOfAccountsApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Accounting\ChartOfAccounts\Models\Account;

/** @mixin Account */
final class AccountResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->resource->getKey(),
            'type' => 'accounting-chart-account',
            'attributes' => [
                'legal_entity_id' => $this->resource->legal_entity_id,
                'parent_id' => $this->resource->parent_id,
                'code' => $this->resource->code,
                'name' => $this->resource->name,
                'description' => $this->resource->description,
                'type' => $this->resource->type?->value,
                'normal_balance' => $this->resource->normal_balance?->value,
                'is_control_account' => $this->resource->is_control_account,
                'allow_manual_entry' => $this->resource->allow_manual_entry,
                'is_active' => $this->resource->is_active,
                'locale' => $this->resource->locale,
                'metadata' => $this->resource->metadata,
            ],
            'children' => self::collection($this->whenLoaded('children')),
        ];
    }
}
