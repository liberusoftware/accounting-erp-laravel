<?php

declare(strict_types=1);

namespace Liberu\Accounting\FixedAssets\Actions;

use Liberu\Accounting\FixedAssets\Exceptions\InvalidAsset;
use Liberu\Accounting\FixedAssets\Models\Asset;

final class UpdateAsset
{
    public function handle(Asset $asset, array $attributes): Asset
    {
        if (isset($attributes['asset_ref']) && blank($attributes['asset_ref'])) {
            throw new InvalidAsset('The asset reference cannot be empty.');
        }
        if (isset($attributes['currency']) && ! preg_match('/^[A-Za-z]{3}$/', $attributes['currency'])) {
            throw new InvalidAsset('Currency must be a three-letter ISO code.');
        }
        if (isset($attributes['asset_ref']) && $attributes['asset_ref'] !== $asset->asset_ref
            && Asset::query()->where('team_id', $asset->team_id)->where('asset_ref', $attributes['asset_ref'])->exists()) {
            throw new InvalidAsset('The asset reference is already in use.');
        }

        $asset->update(array_filter([
            'asset_ref' => $attributes['asset_ref'] ?? null,
            'name' => $attributes['name'] ?? null,
            'currency' => isset($attributes['currency']) ? strtoupper($attributes['currency']) : null,
            'metadata' => $attributes['metadata'] ?? null,
        ], static fn (mixed $value): bool => $value !== null));

        return $asset->refresh();
    }
}
