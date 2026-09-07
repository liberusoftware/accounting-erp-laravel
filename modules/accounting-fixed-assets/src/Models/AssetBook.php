<?php

declare(strict_types=1);

namespace Liberu\Accounting\FixedAssets\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $book_ref
 * @property string $cost
 * @property string $accumulated_depreciation
 * @property string $net_book_value
 */
final class AssetBook extends Model
{
    protected $table = 'accounting_fixed_asset_books';

    protected $fillable = ['asset_id', 'book_ref', 'cost', 'accumulated_depreciation', 'net_book_value', 'last_depreciated_on', 'metadata'];

    protected $casts = ['cost' => 'decimal:2', 'accumulated_depreciation' => 'decimal:2', 'net_book_value' => 'decimal:2', 'last_depreciated_on' => 'date', 'metadata' => 'array'];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }
}
