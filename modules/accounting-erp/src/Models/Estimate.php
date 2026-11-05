<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Liberu\Accounting\Erp\Concerns\HasDocuments;
use Liberu\Accounting\Erp\Contracts\Documentable;
use Liberu\Accounting\Erp\Database\Factories\EstimateFactory;
use Liberu\Accounting\Erp\Traits\IsTenantModel;

/**
 * @property int|null $team_id
 * @property string|null $qbo_id
 * @property string|null $xero_id
 * @property string|null $sage_id
 * @property-read TaxRate|null $taxRate
 * @property-read Collection<int, EstimateItem> $items
 * @property-read Invoice|null $invoice
 */
#[UseFactory(EstimateFactory::class)]
class Estimate extends Model implements Documentable
{
    use HasDocuments;
    use HasFactory, SoftDeletes;
    use IsTenantModel;

    #[\Override]
    protected $primaryKey = 'estimate_id';

    #[\Override]
    protected $fillable = [
        'customer_id',
        'estimate_number',
        'estimate_date',
        'expiration_date',
        'subtotal_amount',
        'tax_amount',
        'total_amount',
        'tax_rate_id',
        'status',
        'invoice_id',
        'sent_at',
        'viewed_at',
        'accepted_at',
        'declined_at',
        'decline_reason',
        'notes',
        'terms',
        'document_path',
        'team_id',
        'qbo_id',
        'xero_id',
        'sage_id',
    ];

    #[\Override]
    protected $casts = [
        'estimate_date' => 'date',
        'expiration_date' => 'date',
        'subtotal_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'sent_at' => 'datetime',
        'viewed_at' => 'datetime',
        'accepted_at' => 'datetime',
        'declined_at' => 'datetime',
    ];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function taxRate()
    {
        // Explicit keys: TaxRate's PK is tax_rate_id, so Laravel's guessed FK
        // (tax_rate_tax_rate_id) is wrong and would always resolve to null.
        return $this->belongsTo(TaxRate::class, 'tax_rate_id', 'tax_rate_id');
    }

    /**
     * @return HasMany<EstimateItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(EstimateItem::class, 'estimate_id', 'estimate_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function salesOrder(): HasOne
    {
        return $this->hasOne(SalesOrder::class, 'estimate_id', 'estimate_id');
    }

    // Calculated Attributes
    public function getIsExpiredAttribute()
    {
        if (! $this->expiration_date || $this->status === 'accepted') {
            return false;
        }

        return Carbon::now()->isAfter($this->expiration_date);
    }

    public function getDaysUntilExpirationAttribute(): ?float
    {
        if (! $this->expiration_date) {
            return null;
        }

        return Carbon::now()->diffInDays($this->expiration_date, false);
    }

    // Business Logic Methods
    public function calculateTax()
    {
        if (! $this->taxRate) {
            return 0;
        }

        $baseAmount = $this->subtotal_amount;
        $previousTaxes = 0;

        if ($this->taxRate->is_compound) {
            $nonCompoundTaxes = TaxRate::where('team_id', $this->team_id)
                ->where('is_active', true)
                ->where('is_compound', false)
                ->get();

            foreach ($nonCompoundTaxes as $tax) {
                $previousTaxes += $tax->calculateTax($baseAmount);
            }
        }

        $taxAmount = $this->taxRate->calculateTax($baseAmount, $previousTaxes);
        $this->tax_amount = $taxAmount;
        $this->total_amount = $this->subtotal_amount + $taxAmount;

        return $taxAmount;
    }

    public function calculateTotals(): void
    {
        $this->subtotal_amount = $this->items->sum('amount');
        $this->calculateTax();
        $this->save();
    }

    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    public function markAsViewed(): void
    {
        if (! $this->viewed_at) {
            $this->update([
                'status' => 'viewed',
                'viewed_at' => now(),
            ]);
        }
    }

    public function accept(): void
    {
        $this->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);
    }

    public function decline($reason = null): void
    {
        $this->update([
            'status' => 'declined',
            'declined_at' => now(),
            'decline_reason' => $reason,
        ]);
    }

    public function convertToInvoice()
    {
        if ($this->status !== 'accepted') {
            throw new \Exception('Only accepted estimates can be converted to invoices.');
        }

        if ($this->invoice_id) {
            throw new \Exception('This estimate has already been converted to an invoice.');
        }

        // The estimate may already be flowing through the sales-order path; block
        // the legacy direct path too, or one estimate ends up with two invoices.
        if ($this->salesOrder()->exists()) {
            throw new \Exception('This estimate already has a sales order.');
        }

        // Create invoice
        $invoice = Invoice::create([
            'customer_id' => $this->customer_id,
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'total_amount' => $this->subtotal_amount,
            'tax_amount' => $this->tax_amount,
            'tax_rate_id' => $this->tax_rate_id,
            'payment_status' => 'pending',
            'notes' => "Converted from Estimate {$this->estimate_number}",
        ]);

        // Copy items (Note: Invoice uses TimeEntry, not invoice items)
        // This would need to be adjusted based on actual invoice structure
        // For now, just link the estimate to the invoice

        $this->update([
            'invoice_id' => $invoice->invoice_id,
        ]);

        return $invoice;
    }

    // Scopes
    public function scopeExpired($query)
    {
        return $query->where('expiration_date', '<', now())
            ->whereNotIn('status', ['accepted', 'declined']);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['draft', 'sent', 'viewed'])
            ->where(function ($q): void {
                $q->whereNull('expiration_date')
                    ->orWhere('expiration_date', '>=', now());
            });
    }

    public function scopeByCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    // Auto-generate estimate number and check expiration
    #[\Override]
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($estimate): void {
            if (empty($estimate->estimate_number)) {
                $estimate->estimate_number = static::generateEstimateNumber();
            }
            if (empty($estimate->status)) {
                $estimate->status = 'draft';
            }
        });

        static::saving(function ($estimate): void {
            // Auto-expire if past expiration date
            if ($estimate->expiration_date &&
                Carbon::now()->isAfter($estimate->expiration_date) &&
                ! in_array($estimate->status, ['accepted', 'declined', 'expired'])) {
                $estimate->status = 'expired';
            }
        });
    }

    public static function generateEstimateNumber(): string
    {
        $prefix = 'EST';
        $year = date('Y');
        $lastEstimate = static::whereYear('created_at', $year)
            ->orderBy('estimate_number', 'desc')
            ->first();

        if (! $lastEstimate) {
            $number = 1;
        } else {
            $parts = explode('-', (string) $lastEstimate->estimate_number);
            $number = isset($parts[1]) ? ((int) $parts[1]) + 1 : 1;
        }

        return $prefix.$year.'-'.str_pad((string) $number, 4, '0', STR_PAD_LEFT);
    }
}
