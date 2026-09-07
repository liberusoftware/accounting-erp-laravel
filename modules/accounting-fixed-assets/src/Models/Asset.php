<?php

declare(strict_types=1);

namespace Liberu\Accounting\FixedAssets\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Liberu\Accounting\FixedAssets\Enums\AssetStatus;

/**
 * @property AssetStatus $status
 * @property int|null $team_id
 * @property string $asset_ref
 * @property string $name
 * @property string $cost
 * @property string $salvage_value
 * @property string $net_book_value
 * @property string $currency
 * @property string|null $location_ref
 * @property string|null $custodian_ref
 * @property Carbon|null $acquired_on
 * @property Carbon|null $capitalized_on
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read AssetCategory|null $category
 * @property-read Collection<int, AssetComponent> $components
 * @property-read Collection<int, AssetBook> $books
 * @property-read Collection<int, AssetDocument> $documents
 */
final class Asset extends Model
{
    protected $table = 'accounting_fixed_assets';

    protected $fillable = [
        'team_id', 'asset_ref', 'name', 'category_id', 'status', 'acquired_on',
        'capitalized_on', 'cost', 'salvage_value', 'net_book_value', 'currency',
        'location_id', 'custodian_id', 'location_ref', 'custodian_ref', 'metadata',
    ];

    protected $casts = [
        'status' => AssetStatus::class,
        'acquired_on' => 'date',
        'capitalized_on' => 'date',
        'cost' => 'decimal:2',
        'salvage_value' => 'decimal:2',
        'net_book_value' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class, 'category_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(AssetLocation::class, 'location_id');
    }

    public function custodian(): BelongsTo
    {
        return $this->belongsTo(AssetCustodian::class, 'custodian_id');
    }

    /** @return HasMany<AssetComponent, $this> */
    public function components(): HasMany
    {
        return $this->hasMany(AssetComponent::class, 'asset_id');
    }

    /** @return HasMany<AssetBook, $this> */
    public function books(): HasMany
    {
        return $this->hasMany(AssetBook::class, 'asset_id');
    }

    /** @return HasMany<AssetDocument, $this> */
    public function documents(): HasMany
    {
        return $this->hasMany(AssetDocument::class, 'asset_id');
    }
}
