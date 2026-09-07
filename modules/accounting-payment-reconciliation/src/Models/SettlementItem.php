<?php

declare(strict_types=1);

namespace Liberu\Accounting\PaymentReconciliation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Accounting\PaymentReconciliation\Enums\SettlementItemStatus;
use Liberu\Accounting\PaymentReconciliation\Enums\SettlementItemType;

/**
 * @property int $id
 * @property int $run_id
 * @property float|string $net_amount
 * @property SettlementItemStatus $status
 * @property SettlementItemType $type
 */
final class SettlementItem extends Model
{
    protected $table = 'accounting_payment_reconciliation_items';

    protected $fillable = ['run_id', 'external_ref', 'type', 'currency', 'gross_amount', 'fee_amount', 'refund_amount', 'dispute_amount', 'net_amount', 'status', 'reference_type', 'reference_id', 'source_payload', 'source_hash', 'metadata'];

    protected $casts = ['type' => SettlementItemType::class, 'status' => SettlementItemStatus::class, 'gross_amount' => 'decimal:2', 'fee_amount' => 'decimal:2', 'refund_amount' => 'decimal:2', 'dispute_amount' => 'decimal:2', 'net_amount' => 'decimal:2', 'source_payload' => 'array', 'metadata' => 'array'];

    public function run(): BelongsTo
    {
        return $this->belongsTo(SettlementRun::class, 'run_id');
    }

    public function matches(): HasMany
    {
        return $this->hasMany(SettlementMatch::class, 'item_id');
    }

    public function amount(): float
    {
        return round((float) $this->net_amount, 2);
    }
}
