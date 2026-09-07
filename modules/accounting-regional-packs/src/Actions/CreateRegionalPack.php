<?php

declare(strict_types=1);

namespace Liberu\Accounting\RegionalPacks\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\RegionalPacks\Enums\RegionalPackStatus;
use Liberu\Accounting\RegionalPacks\Exceptions\InvalidRegionalPack;
use Liberu\Accounting\RegionalPacks\Models\RegionalPack;

final class CreateRegionalPack
{
    public function handle(array $attributes): RegionalPack
    {
        $country = strtoupper((string) ($attributes['country_code'] ?? ''));
        if (! preg_match('/^[A-Z]{2}$/', $country)) {
            throw new InvalidRegionalPack('country_code must be an ISO 3166-1 alpha-2 code.');
        }foreach (['locale', 'currency'] as $field) {
            if (blank($attributes[$field] ?? null)) {
                throw new InvalidRegionalPack($field.' is required.');
            }
        }

        return DB::transaction(fn (): RegionalPack => RegionalPack::create(['country_code' => $country, 'locale' => $attributes['locale'], 'currency' => strtoupper($attributes['currency']), 'version' => $attributes['version'] ?? '1.0.0', 'status' => RegionalPackStatus::Draft, 'effective_from' => $attributes['effective_from'] ?? now()->toDateString(), 'metadata' => $attributes['metadata'] ?? null]));
    }
}
