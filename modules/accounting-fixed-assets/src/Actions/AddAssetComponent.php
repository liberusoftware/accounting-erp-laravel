<?php

declare(strict_types=1);

namespace Liberu\Accounting\FixedAssets\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\FixedAssets\Enums\AssetStatus;
use Liberu\Accounting\FixedAssets\Events\AssetComponentAdded;
use Liberu\Accounting\FixedAssets\Exceptions\InvalidAsset;
use Liberu\Accounting\FixedAssets\Models\Asset;
use Liberu\Accounting\FixedAssets\Models\AssetComponent;

final class AddAssetComponent
{
    public function handle(Asset $asset, array $attributes): AssetComponent
    {
        foreach (['component_ref', 'name', 'cost', 'useful_life_months'] as $field) {
            if (blank($attributes[$field] ?? null)) {
                throw new InvalidAsset("Missing component field [{$field}].");
            }
        }

        $cost = (float) $attributes['cost'];
        if ($cost <= 0 || (int) $attributes['useful_life_months'] < 1) {
            throw new InvalidAsset('Component cost and useful life must be positive.');
        }
        if (in_array($asset->status, [AssetStatus::Disposed, AssetStatus::Archived], true)) {
            throw new InvalidAsset('Components cannot be added to a closed asset.');
        }
        if ($asset->components()->where('component_ref', $attributes['component_ref'])->exists()) {
            throw new InvalidAsset('The component reference is already in use for this asset.');
        }

        $component = DB::transaction(function () use ($asset, $attributes, $cost): AssetComponent {
            $component = $asset->components()->create([
                'component_ref' => $attributes['component_ref'],
                'name' => $attributes['name'],
                'cost' => $cost,
                'useful_life_months' => $attributes['useful_life_months'],
                'metadata' => $attributes['metadata'] ?? null,
            ]);
            $asset->increment('cost', $cost);
            $asset->increment('net_book_value', $cost);
            $asset->books()->each(function ($book) use ($cost): void {
                $book->increment('cost', $cost);
                $book->increment('net_book_value', $cost);
            });

            return $component;
        });

        event(new AssetComponentAdded($asset->refresh(), $component));

        return $component;
    }
}
