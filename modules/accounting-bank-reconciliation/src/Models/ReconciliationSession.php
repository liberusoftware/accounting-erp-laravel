<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankReconciliation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Accounting\BankReconciliation\Enums\ReconciliationStatus;

final class ReconciliationSession extends Model
{
    protected $table = 'accounting_bank_reconciliation_sessions';

    protected $fillable = ['team_id', 'user_id', 'bank_account_id', 'period_start', 'period_end', 'opening_balance', 'statement_balance', 'status', 'signed_off_at', 'signed_off_by', 'metadata'];

    protected $casts = ['period_start' => 'date', 'period_end' => 'date', 'opening_balance' => 'decimal:2', 'statement_balance' => 'decimal:2', 'status' => ReconciliationStatus::class, 'signed_off_at' => 'datetime', 'metadata' => 'array'];

    protected $attributes = ['status' => 'draft'];

    public function entries(): HasMany
    {
        return $this->hasMany(ReconciliationEntry::class, 'session_id');
    }
}
