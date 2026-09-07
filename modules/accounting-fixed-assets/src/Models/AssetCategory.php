<?php

declare(strict_types=1);

namespace Liberu\Accounting\FixedAssets\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int|null $team_id
 * @property string $category_ref
 * @property string $name
 * @property string $asset_account_ref
 * @property string $depreciation_account_ref
 * @property int $useful_life_months
 * @property string $depreciation_method
 * @property-read Collection<int, Asset> $assets
 */
final class AssetCategory extends Model
{
    protected $table = 'accounting_fixed_asset_categories';

    protected $fillable = ['team_id', 'category_ref', 'name', 'asset_account_ref', 'depreciation_account_ref', 'useful_life_months', 'depreciation_method', 'metadata'];

    protected $casts = ['useful_life_months' => 'integer', 'metadata' => 'array'];

    /** @return HasMany<Asset, $this> */
    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'category_id');
    }
}
