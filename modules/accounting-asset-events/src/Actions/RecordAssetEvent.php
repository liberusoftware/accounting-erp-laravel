<?php

declare(strict_types=1);

namespace Liberu\Accounting\AssetEvents\Actions;

use Liberu\Accounting\AssetEvents\Models\AssetEvent;

final class RecordAssetEvent
{
    public function handle(array $attributes): AssetEvent
    {
        foreach (['team_id', 'asset_id', 'event_type', 'event_date'] as $field) {
            if (blank($attributes[$field] ?? null)) {
                throw new \InvalidArgumentException("{$field} is required.");
            }
        }

        return AssetEvent::create($attributes);
    }
}
