<?php

declare(strict_types=1);

namespace Liberu\Accounting\FixedAssets\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\FixedAssets\Enums\AssetStatus;
use Liberu\Accounting\FixedAssets\Events\AssetCapitalized;
use Liberu\Accounting\FixedAssets\Exceptions\InvalidAsset;
use Liberu\Accounting\FixedAssets\Models\Asset;

final class CapitalizeAsset
{
    public function handle(Asset $asset, string $bookReference): Asset
    {
        if ($asset->status !== AssetStatus::Acquired) {
            throw new InvalidAsset('Only acquired assets can be capitalized.');
        }
        if (blank($bookReference)) {
            throw new InvalidAsset('Asset book reference is required.');
        }
        if ($asset->books()->where('book_ref', $bookReference)->exists()) {
            throw new InvalidAsset('The asset book reference is already in use.');
        }

        $asset = DB::transaction(function () use ($asset, $bookReference): Asset {
            $asset->books()->create([
                'book_ref' => $bookReference,
                'cost' => $asset->cost,
                'net_book_value' => $asset->cost,
            ]);
            $asset->update([
                'status' => AssetStatus::Capitalized,
                'capitalized_on' => now()->toDateString(),
            ]);

            return $asset->refresh();
        });
        event(new AssetCapitalized($asset, $bookReference));

        return $asset;
    }
}
