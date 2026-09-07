<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankReconciliation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Accounting\BankReconciliation\Enums\ReconciliationEntryKind;
use Liberu\Accounting\BankReconciliation\Enums\ReconciliationEntryStatus;

final class ReconciliationEntry extends Model
{
    protected $table = 'accounting_bank_reconciliation_entries';

    protected $fillable = ['team_id', 'session_id', 'source_type', 'source_id', 'kind', 'status', 'amount', 'currency', 'confidence', 'description', 'metadata', 'exception_reason', 'confirmed_at', 'confirmed_by'];

    protected $casts = ['kind' => ReconciliationEntryKind::class, 'status' => ReconciliationEntryStatus::class, 'amount' => 'decimal:2', 'confidence' => 'decimal:4', 'metadata' => 'array', 'confirmed_at' => 'datetime'];

    protected $hidden = ['metadata'];

    public function session(): BelongsTo
    {
        return $this->belongsTo(ReconciliationSession::class, 'session_id');
    }
}
