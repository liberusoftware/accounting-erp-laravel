<?php

declare(strict_types=1);

namespace Liberu\Accounting\RegionalPacks\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\RegionalPacks\Enums\RegionalArtifactType;
use Liberu\Accounting\RegionalPacks\Enums\RegionalPackStatus;
use Liberu\Accounting\RegionalPacks\Exceptions\InvalidRegionalPack;
use Liberu\Accounting\RegionalPacks\Models\RegionalPack;

final class PublishRegionalPack
{
    public function handle(RegionalPack $pack, array $artifacts): RegionalPack
    {
        return DB::transaction(function () use ($pack, $artifacts): RegionalPack {
            $required = collect(RegionalArtifactType::cases())->map(fn (RegionalArtifactType $type): string => $type->value);
            $seen = collect($artifacts)->pluck('type');
            if ($required->diff($seen)->isNotEmpty()) {
                throw new InvalidRegionalPack('A regional pack must define every supported artifact type.');
            }foreach ($artifacts as $artifact) {
                $type = RegionalArtifactType::tryFrom((string) ($artifact['type'] ?? ''));
                if ($type === null || blank($artifact['key'] ?? null) || ! is_array($artifact['definition'] ?? null)) {
                    throw new InvalidRegionalPack('Invalid regional artifact definition.');
                }$pack->artifacts()->updateOrCreate(['type' => $type, 'key' => $artifact['key']], ['definition' => $artifact['definition'], 'status' => 'active', 'metadata' => $artifact['metadata'] ?? null]);
            }$pack->update(['status' => RegionalPackStatus::Active]);

            return $pack->load('artifacts');
        });
    }
}
