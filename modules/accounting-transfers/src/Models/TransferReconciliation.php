<?php

declare(strict_types=1);

namespace Liberu\Accounting\Transfers\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class TransferReconciliation extends Model
{
    protected $table = 'accounting_transfer_reconciliations';

    protected $fillable = ['team_id', 'transfer_id', 'external_ref', 'amount', 'reconciled_on', 'status', 'metadata'];

    protected $casts = ['amount' => 'decimal:6', 'reconciled_on' => 'date', 'metadata' => 'array'];

    public function transfer(): BelongsTo
    {
        return $this->belongsTo(Transfer::class);
    }
}
