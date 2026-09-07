<?php

declare(strict_types=1);

namespace Liberu\Accounting\FixedAssets\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int|null $team_id
 * @property string $custodian_ref
 * @property string $name
 * @property string|null $email
 */
final class AssetCustodian extends Model
{
    protected $table = 'accounting_fixed_asset_custodians';

    protected $fillable = ['team_id', 'custodian_ref', 'name', 'email', 'metadata'];

    protected $casts = ['metadata' => 'array'];

    /** @return HasMany<Asset, $this> */
    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'custodian_id');
    }
}
