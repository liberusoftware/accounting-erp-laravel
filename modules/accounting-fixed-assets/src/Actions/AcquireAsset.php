<?php

declare(strict_types=1);

namespace Liberu\Accounting\FixedAssets\Actions;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Liberu\Accounting\FixedAssets\Enums\AssetStatus;
use Liberu\Accounting\FixedAssets\Events\AssetAcquired;
use Liberu\Accounting\FixedAssets\Exceptions\InvalidAsset;
use Liberu\Accounting\FixedAssets\Models\Asset;
use Liberu\Accounting\FixedAssets\Models\AssetCategory;

final class AcquireAsset
{
    public function handle(AssetCategory $category, array $attributes): Asset
    {
        foreach (['asset_ref', 'name', 'currency', 'acquired_on'] as $field) {
            if (blank($attributes[$field] ?? null)) {
                throw new InvalidAsset("Missing asset field [{$field}].");
            }
        }

        $cost = (float) ($attributes['cost'] ?? 0);
        $salvage = (float) ($attributes['salvage_value'] ?? 0);
        if ($cost <= 0 || $salvage < 0 || $salvage > $cost) {
            throw new InvalidAsset('Asset cost and salvage value are invalid.');
        }
        $teamId = isset($attributes['team_id']) ? (int) $attributes['team_id'] : null;
        if ($category->team_id !== $teamId) {
            throw new InvalidAsset('The category belongs to a different team.');
        }
        if (Asset::query()->where('team_id', $teamId)->where('asset_ref', $attributes['asset_ref'])->exists()) {
            throw new InvalidAsset('The asset reference is already in use.');
        }

        try {
            $asset = DB::transaction(fn (): Asset => Asset::create([
                'team_id' => $teamId,
                'asset_ref' => $attributes['asset_ref'],
                'name' => $attributes['name'],
                'category_id' => $category->getKey(),
                'status' => AssetStatus::Acquired,
                'acquired_on' => $attributes['acquired_on'],
                'cost' => $cost,
                'salvage_value' => $salvage,
                'net_book_value' => $cost,
                'currency' => strtoupper($attributes['currency']),
                'location_ref' => $attributes['location_ref'] ?? null,
                'custodian_ref' => $attributes['custodian_ref'] ?? null,
                'metadata' => $attributes['metadata'] ?? null,
            ]));
        } catch (QueryException $exception) {
            throw new InvalidAsset('The asset could not be acquired.', previous: $exception);
        }

        event(new AssetAcquired($asset));

        return $asset;
    }
}
