<?php

declare(strict_types=1);

namespace Liberu\Accounting\PaymentReconciliation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Accounting\PaymentReconciliation\Enums\DriftStatus;

final class ProviderDrift extends Model
{
    protected $table = 'accounting_payment_reconciliation_drifts';

    protected $fillable = ['run_id', 'field', 'expected_value', 'actual_value', 'severity', 'status', 'resolution', 'resolved_by', 'resolved_at', 'metadata'];

    protected $casts = ['status' => DriftStatus::class, 'resolved_at' => 'datetime', 'metadata' => 'array'];

    public function run(): BelongsTo
    {
        return $this->belongsTo(SettlementRun::class, 'run_id');
    }
}
