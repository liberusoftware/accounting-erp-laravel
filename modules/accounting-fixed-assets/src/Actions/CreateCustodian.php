<?php

declare(strict_types=1);

namespace Liberu\Accounting\FixedAssets\Actions;

use Illuminate\Database\QueryException;
use Liberu\Accounting\FixedAssets\Exceptions\InvalidAsset;
use Liberu\Accounting\FixedAssets\Models\AssetCustodian;

final class CreateCustodian
{
    public function handle(array $attributes): AssetCustodian
    {
        foreach (['custodian_ref', 'name'] as $field) {
            if (blank($attributes[$field] ?? null)) {
                throw new InvalidAsset("Missing custodian field [{$field}].");
            }
        }

        $query = AssetCustodian::query()->where('custodian_ref', $attributes['custodian_ref']);
        if ($attributes['team_id'] ?? null) {
            $query->where('team_id', $attributes['team_id']);
        }
        if ($query->exists()) {
            throw new InvalidAsset('The custodian reference is already in use.');
        }

        try {
            return AssetCustodian::create([
                'team_id' => $attributes['team_id'] ?? null,
                'custodian_ref' => $attributes['custodian_ref'],
                'name' => $attributes['name'],
                'email' => $attributes['email'] ?? null,
                'metadata' => $attributes['metadata'] ?? null,
            ]);
        } catch (QueryException $exception) {
            throw new InvalidAsset('The custodian could not be created.', previous: $exception);
        }
    }
}
