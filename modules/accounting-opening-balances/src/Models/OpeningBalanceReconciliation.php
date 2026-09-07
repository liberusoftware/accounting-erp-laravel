<?php

declare(strict_types=1);

namespace Liberu\Accounting\OpeningBalances\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class OpeningBalanceReconciliation extends Model
{
    protected $table = 'accounting_opening_balance_reconciliations';

    protected $fillable = ['batch_id', 'entry_id', 'expected_amount', 'actual_amount', 'variance', 'status', 'external_ref', 'notes', 'metadata'];

    protected $casts = ['expected_amount' => 'decimal:2', 'actual_amount' => 'decimal:2', 'variance' => 'decimal:2', 'metadata' => 'array'];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(OpeningBalanceBatch::class, 'batch_id');
    }

    public function entry(): BelongsTo
    {
        return $this->belongsTo(OpeningBalanceEntry::class, 'entry_id');
    }
}
