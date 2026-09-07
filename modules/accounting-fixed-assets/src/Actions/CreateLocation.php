<?php

declare(strict_types=1);

namespace Liberu\Accounting\FixedAssets\Actions;

use Illuminate\Database\QueryException;
use Liberu\Accounting\FixedAssets\Exceptions\InvalidAsset;
use Liberu\Accounting\FixedAssets\Models\AssetLocation;

final class CreateLocation
{
    public function handle(array $attributes): AssetLocation
    {
        foreach (['location_ref', 'name'] as $field) {
            if (blank($attributes[$field] ?? null)) {
                throw new InvalidAsset("Missing location field [{$field}].");
            }
        }

        $query = AssetLocation::query()->where('location_ref', $attributes['location_ref']);
        if ($attributes['team_id'] ?? null) {
            $query->where('team_id', $attributes['team_id']);
        }
        if ($query->exists()) {
            throw new InvalidAsset('The location reference is already in use.');
        }

        try {
            return AssetLocation::create([
                'team_id' => $attributes['team_id'] ?? null,
                'location_ref' => $attributes['location_ref'],
                'name' => $attributes['name'],
                'metadata' => $attributes['metadata'] ?? null,
            ]);
        } catch (QueryException $exception) {
            throw new InvalidAsset('The location could not be created.', previous: $exception);
        }
    }
}
