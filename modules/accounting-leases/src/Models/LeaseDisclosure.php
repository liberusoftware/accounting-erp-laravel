<?php

declare(strict_types=1);

namespace Liberu\Accounting\Leases\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class LeaseDisclosure extends Model
{
    protected $table = 'accounting_lease_disclosures';

    protected $fillable = ['lease_id', 'as_of_date', 'remaining_liability', 'current_liability', 'non_current_liability', 'future_payments', 'notes', 'metadata'];

    protected $casts = ['as_of_date' => 'date', 'remaining_liability' => 'decimal:2', 'current_liability' => 'decimal:2', 'non_current_liability' => 'decimal:2', 'future_payments' => 'array', 'metadata' => 'array'];

    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class, 'lease_id');
    }
}
