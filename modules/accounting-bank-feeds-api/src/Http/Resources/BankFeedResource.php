<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankFeedsApi\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Accounting\BankFeeds\Models\BankFeedConnection;

/** @mixin BankFeedConnection */
final class BankFeedResource extends JsonResource
{
    public function toArray($request): array
    {
        return ['id' => (string) $this->id, 'type' => 'accounting-bank-feed', 'attributes' => ['provider' => $this->provider, 'name' => $this->name, 'external_reference' => $this->external_reference, 'status' => $this->status?->value, 'institution' => $this->institution?->only(['id', 'provider', 'external_id', 'name', 'country']), 'cursor_present' => filled($this->cursor), 'last_synced_at' => $this->last_synced_at?->toIso8601String(), 'last_error' => $this->last_error, 'last_error_at' => $this->last_error_at?->toIso8601String(), 'metadata' => $this->metadata], 'links' => ['self' => url('/api/v1/accounting/bank-feeds/'.$this->id)]];
    }
}
