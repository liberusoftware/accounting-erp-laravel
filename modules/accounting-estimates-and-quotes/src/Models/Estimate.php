<?php

declare(strict_types=1);

namespace Liberu\Accounting\EstimatesAndQuotes\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Liberu\Accounting\EstimatesAndQuotes\Enums\EstimateStatus;

/**
 * @property EstimateStatus $status
 * @property string $currency
 * @property string $quote_ref
 * @property int $version
 * @property Carbon|null $expires_on
 * @property string|null $converted_ref
 */
final class Estimate extends Model
{
    protected $table = 'accounting_sales_estimates';

    protected $fillable = ['legal_entity_id', 'customer_ref', 'quote_ref', 'name', 'currency', 'status', 'issue_date', 'expires_on', 'version', 'terms', 'brand', 'accepted_at', 'declined_reason', 'converted_ref', 'metadata'];

    protected $casts = ['status' => EstimateStatus::class, 'issue_date' => 'date', 'expires_on' => 'date', 'accepted_at' => 'datetime', 'version' => 'integer', 'metadata' => 'array', 'brand' => 'array'];

    /** @return HasMany<EstimateItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(EstimateItem::class);
    }

    /** @return HasMany<EstimateVersion, $this> */
    public function versions(): HasMany
    {
        return $this->hasMany(EstimateVersion::class);
    }

    /** @return HasMany<EstimateHistory, $this> */
    public function history(): HasMany
    {
        return $this->hasMany(EstimateHistory::class);
    }
}
