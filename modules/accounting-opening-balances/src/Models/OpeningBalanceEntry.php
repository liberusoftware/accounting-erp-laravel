<?php

declare(strict_types=1);

namespace Liberu\Accounting\OpeningBalances\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Accounting\OpeningBalances\Enums\BalanceType;
use Liberu\Accounting\OpeningBalances\Enums\EntryStatus;

/**
 * @property int $id
 * @property int $batch_id
 * @property string $reference_id
 * @property float|string $debit_amount
 * @property float|string $credit_amount
 * @property BalanceType $balance_type
 * @property EntryStatus $status
 */
final class OpeningBalanceEntry extends Model
{
    protected $table = 'accounting_opening_balance_entries';

    protected $fillable = ['batch_id', 'balance_type', 'reference_type', 'reference_id', 'document_ref', 'document_date', 'due_date', 'currency', 'debit_amount', 'credit_amount', 'status', 'description', 'metadata'];

    protected $casts = ['balance_type' => BalanceType::class, 'status' => EntryStatus::class, 'document_date' => 'date', 'due_date' => 'date', 'debit_amount' => 'decimal:2', 'credit_amount' => 'decimal:2', 'metadata' => 'array'];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(OpeningBalanceBatch::class, 'batch_id');
    }

    public function netAmount(): float
    {
        return round((float) $this->debit_amount - (float) $this->credit_amount, 2);
    }
}
