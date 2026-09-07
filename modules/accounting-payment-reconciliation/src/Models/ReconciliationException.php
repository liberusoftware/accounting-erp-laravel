<?php

declare(strict_types=1);

namespace Liberu\Accounting\PaymentReconciliation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Accounting\PaymentReconciliation\Enums\ReconciliationExceptionStatus;

/**
 * @property ReconciliationExceptionStatus $status
 */
final class ReconciliationException extends Model
{
    protected $table = 'accounting_payment_reconciliation_exceptions';

    protected $fillable = ['run_id', 'kind', 'external_ref', 'expected_amount', 'actual_amount', 'currency', 'status', 'severity', 'resolution', 'resolved_by', 'resolved_at', 'metadata'];

    protected $casts = ['expected_amount' => 'decimal:2', 'actual_amount' => 'decimal:2', 'status' => ReconciliationExceptionStatus::class, 'resolved_at' => 'datetime', 'metadata' => 'array'];

    public function run(): BelongsTo
    {
        return $this->belongsTo(SettlementRun::class, 'run_id');
    }
}
