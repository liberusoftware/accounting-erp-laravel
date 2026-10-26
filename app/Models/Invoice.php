<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\Approvable;
use App\Concerns\HasDocuments;
use App\Concerns\Recurring;
use App\Contracts\ApprovableRecord;
use App\Contracts\Documentable;
use App\Traits\IsTenantModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property int|null $team_id
 * @property int $invoice_id
 * @property int|null $journal_entry_id
 * @property Customer|null $customer
 * @property \Illuminate\Support\Carbon|null $last_reminder_sent_at
 * @property-read Collection<int, TimeEntry> $timeEntries
 * @property-read Team|null $team
 */
class Invoice extends Model implements ApprovableRecord, Documentable
{
    use Approvable;
    use HasDocuments;
    use HasFactory;
    use IsTenantModel;
    use Recurring;

    // protected $primaryKey = "invoice_id";

    #[\Override]
    protected $fillable = [
        'customer_id',
        'sales_order_id',
        'vendor_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'total_amount',
        'payment_status',
        'is_recurring',
        'recurrence_frequency',
        'recurrence_start',
        'recurrence_end',
        'last_generated',
        'approval_status',
        'rejection_reason',
        'approved_by',
        'approved_at',
        'document_path',
        'notes',
        'team_id',
        'journal_entry_id',
        'qbo_id',
        'qbo_sync_token',
        'xero_id',
        'sage_id',
    ];

    #[\Override]
    protected $casts = [
        'total_amount' => 'decimal:2',
        'invoice_date' => 'date',
        'due_date' => 'date',
        'is_recurring' => 'boolean',
        'recurrence_start' => 'date',
        'recurrence_end' => 'date',
        'last_generated' => 'date',
        'approved_at' => 'datetime',
        'last_reminder_sent_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'vendor_id');
    }

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class, 'sales_order_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function timeEntries()
    {
        return $this->hasMany(TimeEntry::class, 'invoice_id');
    }

    /** @return HasMany<InvoiceItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'invoice_id');
    }

    public function recomputePaymentStatus(): void
    {
        $paid = (float) $this->payments()->whereNotNull('journal_entry_id')->sum('payment_amount');
        $total = (float) $this->total_amount;

        if ($total > 0.0 && $paid >= $total) {
            $this->payment_status = 'paid';
        } elseif ($paid > 0.0) {
            $this->payment_status = 'partial';
        } else {
            $this->payment_status = 'pending';
        }

        $this->save();
    }

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class);
    }

    /**
     * Roll the line-item amounts up into the invoice total.
     */
    public function calculateTotals(): void
    {
        $this->total_amount = (float) $this->items()->sum('amount');
        $this->save();
    }

    /**
     * Compute and persist this invoice's late fee, returning the amount charged.
     *
     * // ponytail: flat percentage of total_amount, charged once the due date plus
     * // grace period has fully passed; recompute overwrites (idempotent — the command
     * // reruns, so accumulating would double-charge). No daily accrual / compounding —
     * // add that only if a real late-fee spec ever calls for it.
     */
    public function calculateLateFee(): float
    {
        if ($this->payment_status === 'paid' || $this->due_date === null) {
            return 0.0;
        }

        $overdueAfter = $this->due_date->copy()->addDays((int) $this->grace_period_days);

        if (today()->lte($overdueAfter)) {
            return 0.0;
        }

        $fee = round((float) $this->total_amount * (float) $this->late_fee_percentage / 100, 2);

        $this->late_fee_amount = $fee;
        $this->save();

        return $fee;
    }

    public function creditMemos()
    {
        return $this->hasMany(CreditMemo::class, 'invoice_id');
    }

    // ponytail: invoice-level tax removed — the live `invoices` table has no
    // tax_amount / tax_rate_id columns, so the old calculateTax() was silently
    // inert (it early-returned on the always-null taxRate relation). Tax lives in
    // exactly ONE place now: per-line on invoice_items (InvoiceItem::calculateAmount
    // + its tax_amount column). getTotalWithTax() sums that line-item tax.
    public function getTotalWithTax(): float
    {
        return (float) $this->total_amount + (float) $this->items()->sum('tax_amount');
    }

    public function calculateTotalFromTimeEntries()
    {
        $this->total_amount = $this->timeEntries->sum('total_amount');

        return $this->total_amount;
    }

    public function generatePDF()
    {
        $data = [
            'invoice' => $this,
            'customer' => $this->customer,
            'vendor' => $this->vendor,
        ];

        $pdf = Pdf::loadView('invoices.template', $data);

        return $pdf->download('invoice_'.$this->invoice_number.'.pdf');
    }

    protected function recurringNumberColumn(): ?string
    {
        return 'invoice_number';
    }

    protected function recurringItemsRelation(): ?string
    {
        return 'items';
    }

    /**
     * @return array<string, mixed>
     */
    protected function recurringDraftAttributes(): array
    {
        return [
            'payment_status' => 'pending',
            'approval_status' => 'pending',
            'approved_by' => null,
            'approved_at' => null,
            'rejection_reason' => null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function recurringDateColumns(Carbon $date): array
    {
        return [
            'invoice_date' => $date->copy(),
            'due_date' => $date->copy()->addDays(30),
        ];
    }

    public function approvalAmount(): float
    {
        return (float) $this->total_amount;
    }

    public function approve(): void
    {
        $this->markApproved();
    }

    public function reject(?string $reason): void
    {
        $this->markRejected($reason);
    }

    public function isPending(): bool
    {
        return $this->approval_status === 'pending';
    }

    #[\Override]
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice): void {
            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = 'INV-'.str_pad((string) ((int) static::max('id') + 1), 6, '0', STR_PAD_LEFT);
            }
            if (empty($invoice->approval_status)) {
                $invoice->approval_status = 'pending';
            }
        });
    }
}
