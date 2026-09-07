<?php

declare(strict_types=1);

namespace Liberu\Accounting\Leases\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Accounting\Leases\Enums\PaymentStatus;

/**
 * @property PaymentStatus $status
 * @property string $principal_amount
 * @property string $interest_amount
 * @property string $depreciation_amount
 */
final class LeasePayment extends Model
{
    protected $table = 'accounting_lease_payments';

    protected $fillable = ['lease_id', 'payment_ref', 'due_date', 'amount', 'principal_amount', 'interest_amount', 'depreciation_amount', 'status', 'posted_at', 'metadata'];

    protected $casts = ['due_date' => 'date', 'amount' => 'decimal:2', 'principal_amount' => 'decimal:2', 'interest_amount' => 'decimal:2', 'depreciation_amount' => 'decimal:2', 'status' => PaymentStatus::class, 'posted_at' => 'datetime', 'metadata' => 'array'];

    /** @return BelongsTo<Lease, $this> */
    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class, 'lease_id');
    }
}
