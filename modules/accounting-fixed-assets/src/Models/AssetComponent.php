<?php

declare(strict_types=1);

namespace Liberu\Accounting\FixedAssets\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $component_ref
 * @property string $name
 * @property string $cost
 * @property int $useful_life_months
 */
final class AssetComponent extends Model
{
    protected $table = 'accounting_fixed_asset_components';

    protected $fillable = ['asset_id', 'component_ref', 'name', 'cost', 'useful_life_months', 'metadata'];

    protected $casts = ['cost' => 'decimal:2', 'useful_life_months' => 'integer', 'metadata' => 'array'];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }
}
