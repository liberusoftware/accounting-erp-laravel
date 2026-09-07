<?php

declare(strict_types=1);

namespace Liberu\Accounting\FixedAssets\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\FixedAssets\Enums\AssetStatus;
use Liberu\Accounting\FixedAssets\Events\AssetDisposed;
use Liberu\Accounting\FixedAssets\Exceptions\InvalidAsset;
use Liberu\Accounting\FixedAssets\Models\Asset;

final class DisposeAsset
{
    public function handle(Asset $asset, string $reason): Asset
    {
        if ($asset->status !== AssetStatus::Capitalized) {
            throw new InvalidAsset('Only capitalized assets can be disposed.');
        }
        if (blank($reason)) {
            throw new InvalidAsset('A disposal reason is required.');
        }

        $asset = DB::transaction(function () use ($asset, $reason): Asset {
            $metadata = $asset->metadata ?? [];
            $metadata['disposal'] = ['reason' => $reason, 'at' => now()->toIso8601String()];
            $asset->update(['status' => AssetStatus::Disposed, 'metadata' => $metadata]);

            return $asset->refresh();
        });
        event(new AssetDisposed($asset));

        return $asset;
    }
}
