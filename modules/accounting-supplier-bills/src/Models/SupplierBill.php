<?php

declare(strict_types=1);

namespace Liberu\Accounting\SupplierBills\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Liberu\Accounting\FinancialMasterData\Models\Party;
use Liberu\Accounting\SupplierBills\Enums\PaymentStatus;
use Liberu\Accounting\SupplierBills\Enums\SupplierBillStatus;

/**
 * @property int $id
 * @property int $party_id
 * @property string $bill_number
 * @property Carbon $bill_date
 * @property Carbon|null $due_on
 * @property SupplierBillStatus $status
 * @property PaymentStatus $payment_status
 * @property float|string $subtotal
 * @property float|string $tax_total
 * @property float|string $total
 * @property float|string $amount_paid
 * @property string $currency
 * @property string|null $capture_source
 * @property string|null $purchase_order_reference
 * @property string|null $reference_number
 * @property bool $recurring
 * @property string $approval_status
 * @property string|null $rejection_reason
 * @property array<string,mixed>|null $external_ids
 * @property array<string,mixed>|null $metadata
 */
final class SupplierBill extends Model
{
    protected $table = 'accounting_supplier_bills';

    protected $fillable = ['party_id', 'bill_number', 'bill_date', 'due_on', 'status', 'payment_status', 'subtotal', 'tax_total', 'total', 'amount_paid', 'currency', 'capture_source', 'purchase_order_reference', 'reference_number', 'notes', 'approval_status', 'approved_by', 'approved_at', 'rejection_reason', 'recurring', 'recurrence_frequency', 'recurrence_start', 'recurrence_end', 'last_generated', 'external_ids', 'metadata'];

    protected $casts = ['bill_date' => 'date', 'due_on' => 'date', 'status' => SupplierBillStatus::class, 'payment_status' => PaymentStatus::class, 'subtotal' => 'decimal:2', 'tax_total' => 'decimal:2', 'total' => 'decimal:2', 'amount_paid' => 'decimal:2', 'approved_at' => 'datetime', 'recurring' => 'boolean', 'recurrence_start' => 'date', 'recurrence_end' => 'date', 'last_generated' => 'date', 'external_ids' => 'array', 'metadata' => 'array'];

    protected $attributes = ['status' => 'draft', 'payment_status' => 'unpaid', 'amount_paid' => 0, 'tax_total' => 0, 'subtotal' => 0, 'total' => 0, 'currency' => 'USD'];

    protected static function booted(): void
    {
        self::updating(function (self $bill): void {
            $original = $bill->getRawOriginal('status');
            if (in_array($original, [SupplierBillStatus::Posted->value, SupplierBillStatus::Void->value], true)) {
                $allowed = $original === SupplierBillStatus::Posted->value ? ['status', 'payment_status', 'amount_paid', 'metadata'] : [];
                if (array_diff(array_keys($bill->getDirty()), $allowed) !== []) {
                    throw new \LogicException('Posted and void supplier bills are immutable.');
                }
            }
        });
    }

    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }

    /** @return HasMany<SupplierBillLine, $this> */
    public function lines(): HasMany
    {
        return $this->hasMany(SupplierBillLine::class, 'bill_id');
    }

    /** @return HasMany<SupplierBillCredit, $this> */
    public function credits(): HasMany
    {
        return $this->hasMany(SupplierBillCredit::class, 'bill_id');
    }

    /** @return HasMany<SupplierBillDocument, $this> */
    public function documents(): HasMany
    {
        return $this->hasMany(SupplierBillDocument::class, 'bill_id');
    }

    /** @return HasMany<SupplierBillMatch, $this> */
    public function matches(): HasMany
    {
        return $this->hasMany(SupplierBillMatch::class, 'bill_id');
    }

    public function outstanding(): float
    {
        return max(0, (float) $this->total - (float) $this->credits()->sum('amount') - (float) $this->amount_paid);
    }

    public function isOverdue(?\DateTimeInterface $asOf = null): bool
    {
        return $this->due_on !== null && $this->outstanding() > 0 && $this->due_on->isBefore($asOf ?? now());
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereIn('status', [SupplierBillStatus::Approved->value, SupplierBillStatus::Posted->value])->where('payment_status', '!=', PaymentStatus::Paid->value);
    }
}
