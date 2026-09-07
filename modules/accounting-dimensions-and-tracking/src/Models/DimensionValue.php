<?php

declare(strict_types=1);

namespace Liberu\Accounting\Dimensions\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $dimension_id
 * @property string $code
 * @property string $name
 * @property bool $is_active
 */
class DimensionValue extends Model
{
    protected $table = 'accounting_dimension_values';

    protected $fillable = ['dimension_id', 'code', 'name', 'parent_id', 'is_active', 'metadata'];

    protected $casts = ['is_active' => 'bool', 'metadata' => 'array'];

    public function dimension(): BelongsTo
    {
        return $this->belongsTo(Dimension::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }
}
