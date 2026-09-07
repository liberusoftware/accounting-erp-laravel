<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesInvoicing\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Liberu\Accounting\FinancialMasterData\Models\Party;
use Liberu\Accounting\SalesInvoicing\Enums\InvoiceStatus;

/**
 * @property int $id
 * @property string $invoice_number
 * @property Carbon $invoice_date
 * @property Carbon|null $due_on
 * @property int|null $party_id
 * @property InvoiceStatus $status
 * @property float|string $subtotal
 * @property float|string $discount_total
 * @property float|string $tax_total
 * @property float|string $total
 * @property string $currency
 * @property string|null $delivery_status
 * @property Carbon|null $delivered_at
 * @property-read Collection<int, SalesInvoiceLine> $lines
 * @property-read Collection<int, SalesInvoiceDeposit> $deposits
 */
final class SalesInvoice extends Model
{
    protected $table = 'accounting_sales_invoices';

    protected $fillable = ['invoice_number', 'party_id', 'invoice_date', 'due_on', 'status', 'subtotal', 'discount_total', 'tax_total', 'total', 'currency', 'notes', 'branding', 'recurring_source', 'delivery_status', 'delivered_at', 'metadata'];

    protected $casts = ['invoice_date' => 'date', 'due_on' => 'date', 'delivered_at' => 'datetime', 'status' => InvoiceStatus::class, 'subtotal' => 'decimal:2', 'discount_total' => 'decimal:2', 'tax_total' => 'decimal:2', 'total' => 'decimal:2', 'branding' => 'array', 'recurring_source' => 'array', 'metadata' => 'array'];

    protected static function booted(): void
    {
        self::updating(function (self $invoice): void {
            $original = $invoice->getRawOriginal('status');
            $dirty = array_keys($invoice->getDirty());
            $allowed = match ($original) {
                InvoiceStatus::Draft->value => $invoice->getFillable(), InvoiceStatus::Approved->value => ['status'], InvoiceStatus::Final->value => ['delivery_status', 'delivered_at'], default => [],
            };
            if (array_diff($dirty, $allowed) !== []) {
                throw new \LogicException('Approved and final invoices are immutable.');
            }
        });
        self::deleting(function (self $invoice): void {
            if ($invoice->status !== InvoiceStatus::Draft) {
                throw new \LogicException('Approved and final invoices cannot be deleted.');
            }
        });
    }

    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(SalesInvoiceLine::class, 'invoice_id');
    }

    public function deposits(): HasMany
    {
        return $this->hasMany(SalesInvoiceDeposit::class, 'invoice_id');
    }

    public function depositedTotal(): float
    {
        return (float) $this->deposits()->sum('amount');
    }

    public function outstanding(): float
    {
        return max(0, (float) $this->total - $this->depositedTotal());
    }
}
