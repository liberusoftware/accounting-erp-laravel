<?php

declare(strict_types=1);

namespace Liberu\Accounting\ThreeWayMatching\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Liberu\Accounting\ThreeWayMatching\Enums\MatchStatus;

/**
 * @property int $id
 * @property string $purchase_order_type
 * @property string $purchase_order_id
 * @property string $receipt_type
 * @property string $receipt_id
 * @property string $bill_type
 * @property string $bill_id
 * @property float|string $ordered_quantity
 * @property float|string $received_quantity
 * @property float|string $billed_quantity
 * @property float|string $ordered_unit_price
 * @property float|string $billed_unit_price
 * @property float|string $expected_tax
 * @property float|string $billed_tax
 * @property float|string $quantity_tolerance
 * @property float|string $price_tolerance
 * @property float|string $tax_tolerance
 * @property MatchStatus $status
 * @property string $currency
 * @property int|null $approved_by
 * @property Carbon|null $approved_at
 * @property string|null $rejected_reason
 * @property array<string,mixed>|null $metadata
 * @property-read Collection<int, MatchException> $exceptions
 * @property-read Collection<int, MatchEvidence> $evidence
 */
final class MatchRecord extends Model
{
    protected $table = 'accounting_three_way_matches';

    protected $fillable = ['purchase_order_type', 'purchase_order_id', 'receipt_type', 'receipt_id', 'bill_type', 'bill_id', 'currency', 'ordered_quantity', 'received_quantity', 'billed_quantity', 'ordered_unit_price', 'billed_unit_price', 'expected_tax', 'billed_tax', 'quantity_tolerance', 'price_tolerance', 'tax_tolerance', 'status', 'approved_by', 'approved_at', 'rejected_reason', 'metadata'];

    protected $casts = ['ordered_quantity' => 'decimal:4', 'received_quantity' => 'decimal:4', 'billed_quantity' => 'decimal:4', 'ordered_unit_price' => 'decimal:4', 'billed_unit_price' => 'decimal:4', 'expected_tax' => 'decimal:2', 'billed_tax' => 'decimal:2', 'quantity_tolerance' => 'decimal:4', 'price_tolerance' => 'decimal:4', 'tax_tolerance' => 'decimal:2', 'status' => MatchStatus::class, 'approved_at' => 'datetime', 'metadata' => 'array'];

    public function exceptions(): HasMany
    {
        return $this->hasMany(MatchException::class, 'match_id');
    }

    public function evidence(): HasMany
    {
        return $this->hasMany(MatchEvidence::class, 'match_id');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->whereIn('status', [MatchStatus::Matched->value, MatchStatus::Partial->value, MatchStatus::Exception->value]);
    }

    public function hasBlockingExceptions(): bool
    {
        return $this->exceptions()->where('severity', 'blocking')->where('status', 'open')->exists();
    }
}
