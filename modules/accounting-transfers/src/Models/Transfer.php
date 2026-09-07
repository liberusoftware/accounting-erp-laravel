<?php

declare(strict_types=1);

namespace Liberu\Accounting\Transfers\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Accounting\Transfers\Enums\TransferStatus;

final class Transfer extends Model
{
    protected $table = 'accounting_transfers';

    protected $fillable = ['team_id', 'source_account_ref', 'destination_account_ref', 'source_currency', 'destination_currency', 'source_amount', 'destination_amount', 'exchange_rate', 'fee_amount', 'status', 'reference', 'metadata'];

    protected $casts = ['source_amount' => 'decimal:6', 'destination_amount' => 'decimal:6', 'exchange_rate' => 'decimal:10', 'fee_amount' => 'decimal:6', 'status' => TransferStatus::class, 'metadata' => 'array'];

    public function reconciliations(): HasMany
    {
        return $this->hasMany(TransferReconciliation::class);
    }
}
