<?php

declare(strict_types=1);

namespace Liberu\Accounting\DepositsAndClearing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Liberu\Accounting\DepositsAndClearing\Enums\ClearingDepositStatus;

final class ClearingDeposit extends Model
{
    protected $table = 'accounting_clearing_deposits';

    protected $fillable = ['team_id', 'deposit_ref', 'provider', 'account_ref', 'currency', 'gross_amount', 'fee_amount', 'payout_amount', 'deposit_date', 'status', 'metadata'];

    protected $casts = ['gross_amount' => 'decimal:2', 'fee_amount' => 'decimal:2', 'payout_amount' => 'decimal:2', 'deposit_date' => 'date', 'status' => ClearingDepositStatus::class, 'metadata' => 'array'];

    public function funds(): HasMany
    {
        return $this->hasMany(ClearingFund::class, 'deposit_id');
    }
}
