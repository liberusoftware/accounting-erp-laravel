<?php

declare(strict_types=1);

namespace Liberu\Accounting\FixedAssets\Actions;

use Illuminate\Database\QueryException;
use Liberu\Accounting\FixedAssets\Exceptions\InvalidAsset;
use Liberu\Accounting\FixedAssets\Models\AssetCategory;

final class CreateCategory
{
    public function handle(array $attributes): AssetCategory
    {
        foreach (['category_ref', 'name', 'asset_account_ref', 'depreciation_account_ref', 'useful_life_months', 'depreciation_method'] as $field) {
            if (blank($attributes[$field] ?? null)) {
                throw new InvalidAsset("Missing category field [{$field}].");
            }
        }

        if ((int) $attributes['useful_life_months'] < 1 || ! preg_match('/^[A-Za-z0-9_.-]+$/', (string) $attributes['category_ref'])) {
            throw new InvalidAsset('Category reference and useful life are invalid.');
        }

        $query = AssetCategory::query()->where('category_ref', $attributes['category_ref']);
        if (array_key_exists('team_id', $attributes)) {
            $query->where('team_id', $attributes['team_id']);
        }
        if ($query->exists()) {
            throw new InvalidAsset('The category reference is already in use.');
        }

        try {
            return AssetCategory::create([
                'team_id' => $attributes['team_id'] ?? null,
                'category_ref' => $attributes['category_ref'],
                'name' => $attributes['name'],
                'asset_account_ref' => $attributes['asset_account_ref'],
                'depreciation_account_ref' => $attributes['depreciation_account_ref'],
                'useful_life_months' => $attributes['useful_life_months'],
                'depreciation_method' => $attributes['depreciation_method'],
                'metadata' => $attributes['metadata'] ?? null,
            ]);
        } catch (QueryException $exception) {
            throw new InvalidAsset('The category could not be created.', previous: $exception);
        }
    }
}
