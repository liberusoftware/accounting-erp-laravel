<?php

declare(strict_types=1);

namespace Liberu\Accounting\PaymentReconciliation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class AuditEvidence extends Model
{
    public $timestamps = false;

    protected $table = 'accounting_payment_reconciliation_audits';

    protected $fillable = ['run_id', 'event_type', 'actor_id', 'payload', 'payload_hash', 'created_at'];

    protected $casts = ['payload' => 'array', 'created_at' => 'datetime'];

    public function run(): BelongsTo
    {
        return $this->belongsTo(SettlementRun::class, 'run_id');
    }
}
