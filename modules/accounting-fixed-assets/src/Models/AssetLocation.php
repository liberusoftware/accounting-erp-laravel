<?php

declare(strict_types=1);

namespace Liberu\Accounting\FixedAssets\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int|null $team_id
 * @property string $location_ref
 * @property string $name
 */
final class AssetLocation extends Model
{
    protected $table = 'accounting_fixed_asset_locations';

    protected $fillable = ['team_id', 'location_ref', 'name', 'metadata'];

    protected $casts = ['metadata' => 'array'];

    /** @return HasMany<Asset, $this> */
    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'location_id');
    }
}
