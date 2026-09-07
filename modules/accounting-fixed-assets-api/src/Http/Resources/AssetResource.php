<?php

declare(strict_types=1);

namespace Liberu\Accounting\FixedAssetsApi\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Liberu\Accounting\FixedAssets\Models\Asset;

/** @mixin Asset */
final class AssetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Asset $asset */
        $asset = $this->resource;

        return [
            'id' => (string) $asset->getKey(),
            'type' => 'accounting-fixed-assets',
            'attributes' => [
                'asset_ref' => $asset->asset_ref,
                'name' => $asset->name,
                'status' => $asset->status->value,
                'acquired_on' => $asset->acquired_on?->toIso8601String(),
                'capitalized_on' => $asset->capitalized_on?->toIso8601String(),
                'cost' => $this->money($asset->cost, $asset->currency),
                'salvage_value' => $this->money($asset->salvage_value, $asset->currency),
                'net_book_value' => $this->money($asset->net_book_value, $asset->currency),
                'currency' => $asset->currency,
                'location_ref' => $asset->location_ref,
                'custodian_ref' => $asset->custodian_ref,
            ],
            'relationships' => [
                'category' => $this->whenLoaded('category', fn (): ?array => $asset->category === null ? null : [
                    'id' => (string) $asset->category->getKey(),
                    'category_ref' => $asset->category->category_ref,
                    'name' => $asset->category->name,
                ]),
                'components' => $this->whenLoaded('components', fn (): array => $asset->components->map(fn ($component): array => [
                    'id' => (string) $component->getKey(),
                    'component_ref' => $component->component_ref,
                    'name' => $component->name,
                    'cost' => $this->money($component->cost, $asset->currency),
                    'useful_life_months' => $component->useful_life_months,
                ])->all()),
                'books' => $this->whenLoaded('books', fn (): array => $asset->books->map(fn ($book): array => [
                    'id' => (string) $book->getKey(),
                    'book_ref' => $book->book_ref,
                    'cost' => $this->money($book->cost, $asset->currency),
                    'accumulated_depreciation' => $this->money($book->accumulated_depreciation, $asset->currency),
                    'net_book_value' => $this->money($book->net_book_value, $asset->currency),
                ])->all()),
                'documents' => $this->whenLoaded('documents', fn (): array => $asset->documents->map(fn ($document): array => [
                    'id' => (string) $document->getKey(),
                    'document_ref' => $document->document_ref,
                    'kind' => $document->kind,
                    'file_ref' => $document->file_ref,
                    'description' => $document->description,
                    'checksum' => $document->checksum,
                    'attached_at' => $document->attached_at->toIso8601String(),
                ])->all()),
            ],
            'meta' => [
                'created_at' => $asset->created_at?->toIso8601String(),
                'updated_at' => $asset->updated_at?->toIso8601String(),
            ],
        ];
    }

    private function money(string|float|int|null $amount, string $currency): array
    {
        return ['amount' => (string) $amount, 'currency' => $currency];
    }
}
