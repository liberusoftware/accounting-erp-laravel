<?php

declare(strict_types=1);

namespace Liberu\Accounting\PaymentReconciliation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Accounting\PaymentReconciliation\Enums\SettlementStatus;

/**
 * @property int $id
 * @property int|null $team_id
 * @property string $currency
 * @property string $source_hash
 * @property SettlementStatus $status
 * @property array<string,mixed>|null $metadata
 */
final class SettlementRun extends Model
{
    protected $table = 'accounting_payment_reconciliation_runs';

    protected $fillable = ['team_id', 'provider', 'merchant_ref', 'settlement_ref', 'period_start', 'period_end', 'currency', 'gross_amount', 'fee_amount', 'refund_amount', 'dispute_amount', 'net_amount', 'status', 'idempotency_key', 'source_hash', 'failure_message', 'metadata'];

    protected $casts = ['gross_amount' => 'decimal:2', 'fee_amount' => 'decimal:2', 'refund_amount' => 'decimal:2', 'dispute_amount' => 'decimal:2', 'net_amount' => 'decimal:2', 'status' => SettlementStatus::class, 'period_start' => 'date', 'period_end' => 'date', 'metadata' => 'array'];

    public function items(): HasMany
    {
        return $this->hasMany(SettlementItem::class, 'run_id');
    }

    public function exceptions(): HasMany
    {
        return $this->hasMany(ReconciliationException::class, 'run_id');
    }

    public function drifts(): HasMany
    {
        return $this->hasMany(ProviderDrift::class, 'run_id');
    }

    public function audits(): HasMany
    {
        return $this->hasMany(AuditEvidence::class, 'run_id');
    }
}
