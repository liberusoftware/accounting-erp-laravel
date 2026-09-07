<?php

declare(strict_types=1);

namespace Liberu\Accounting\Dimensions\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Accounting\Dimensions\Enums\DimensionKind;

/**
 * @property string $code
 * @property string $name
 * @property string|null $description
 * @property DimensionKind $kind
 * @property bool $is_required
 * @property bool $is_active
 */
class Dimension extends Model
{
    protected $table = 'accounting_dimensions';

    protected $fillable = ['code', 'name', 'kind', 'description', 'is_required', 'is_active', 'metadata'];

    protected $casts = ['kind' => DimensionKind::class, 'is_required' => 'bool', 'is_active' => 'bool', 'metadata' => 'array'];

    /** @return HasMany<DimensionValue, $this> */
    public function values(): HasMany
    {
        return $this->hasMany(DimensionValue::class);
    }
}
