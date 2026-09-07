<?php

declare(strict_types=1);

namespace Liberu\Accounting\Leases\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class LeaseModification extends Model
{
    protected $table = 'accounting_lease_modifications';

    protected $fillable = ['lease_id', 'modification_ref', 'effective_date', 'kind', 'old_term_end', 'new_term_end', 'old_payment_amount', 'new_payment_amount', 'adjustment_amount', 'reason', 'metadata'];

    protected $casts = ['effective_date' => 'date', 'old_term_end' => 'date', 'new_term_end' => 'date', 'old_payment_amount' => 'decimal:2', 'new_payment_amount' => 'decimal:2', 'adjustment_amount' => 'decimal:2', 'metadata' => 'array'];

    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class, 'lease_id');
    }
}
