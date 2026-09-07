<?php

declare(strict_types=1);

namespace Liberu\Accounting\FixedAssets\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\FixedAssets\Events\AssetDocumentAdded;
use Liberu\Accounting\FixedAssets\Exceptions\InvalidAsset;
use Liberu\Accounting\FixedAssets\Models\Asset;
use Liberu\Accounting\FixedAssets\Models\AssetDocument;

final class AddAssetDocument
{
    public function handle(Asset $asset, array $attributes): AssetDocument
    {
        foreach (['document_ref', 'kind', 'file_ref', 'attached_by'] as $field) {
            if (blank($attributes[$field] ?? null)) {
                throw new InvalidAsset("Missing document field [{$field}].");
            }
        }
        if ($asset->documents()->where('document_ref', $attributes['document_ref'])->exists()) {
            throw new InvalidAsset('The document reference is already attached to this asset.');
        }

        $document = DB::transaction(fn (): AssetDocument => $asset->documents()->create([
            'document_ref' => $attributes['document_ref'],
            'kind' => $attributes['kind'],
            'file_ref' => $attributes['file_ref'],
            'description' => $attributes['description'] ?? null,
            'checksum' => $attributes['checksum'] ?? null,
            'attached_by' => $attributes['attached_by'],
            'attached_at' => now(),
            'metadata' => $attributes['metadata'] ?? null,
        ]));
        event(new AssetDocumentAdded($asset, $document));

        return $document;
    }
}
