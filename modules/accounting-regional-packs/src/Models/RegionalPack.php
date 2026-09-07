<?php

declare(strict_types=1);

namespace Liberu\Accounting\RegionalPacks\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Accounting\RegionalPacks\Enums\RegionalPackStatus;

/**
 * @property int $id
 * @property string $country_code
 * @property string $locale
 * @property string $currency
 * @property RegionalPackStatus $status
 * @property array<string,mixed>|null $metadata
 */
final class RegionalPack extends Model
{
    protected $table = 'accounting_regional_packs';

    protected $fillable = ['country_code', 'locale', 'currency', 'version', 'status', 'effective_from', 'effective_to', 'metadata'];

    protected $casts = ['status' => RegionalPackStatus::class, 'effective_from' => 'date', 'effective_to' => 'date', 'metadata' => 'array'];

    /** @return HasMany<RegionalPackArtifact, $this> */
    public function artifacts(): HasMany
    {
        return $this->hasMany(RegionalPackArtifact::class, 'pack_id');
    }
}
