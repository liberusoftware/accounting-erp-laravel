<?php

declare(strict_types=1);

namespace Liberu\Accounting\Mileage\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class MileageReimbursement extends Model
{
    protected $table = 'accounting_mileage_reimbursements';

    protected $fillable = ['trip_id', 'payee_ref', 'currency', 'amount', 'status', 'external_ref', 'failure_message', 'paid_at', 'metadata'];

    protected $casts = ['amount' => 'decimal:2', 'paid_at' => 'datetime', 'metadata' => 'array'];

    public function trip(): BelongsTo
    {
        return $this->belongsTo(MileageTrip::class, 'trip_id');
    }
}
