<?php

declare(strict_types=1);

namespace Liberu\Accounting\Mileage\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Accounting\Mileage\Enums\ApprovalDecision;

/** @property ApprovalDecision $decision */
final class MileageApproval extends Model
{
    protected $table = 'accounting_mileage_approvals';

    protected $fillable = ['trip_id', 'actor_ref', 'decision', 'reason', 'decided_at', 'metadata'];

    protected $casts = ['decision' => ApprovalDecision::class, 'decided_at' => 'datetime', 'metadata' => 'array'];

    public function trip(): BelongsTo
    {
        return $this->belongsTo(MileageTrip::class, 'trip_id');
    }
}
