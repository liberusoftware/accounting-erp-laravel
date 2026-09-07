<?php

declare(strict_types=1);

namespace Liberu\Accounting\DepositsAndClearing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Liberu\Accounting\DepositsAndClearing\Enums\ClearingFundStatus;

final class ClearingFund extends Model
{
    protected $table = 'accounting_clearing_funds';

    protected $fillable = ['team_id', 'deposit_id', 'source_type', 'source_id', 'amount', 'currency', 'received_on', 'status', 'metadata'];

    protected $casts = ['amount' => 'decimal:2', 'received_on' => 'date', 'status' => ClearingFundStatus::class, 'metadata' => 'array'];

    public function deposit(): BelongsTo
    {
        return $this->belongsTo(ClearingDeposit::class, 'deposit_id');
    }
}
