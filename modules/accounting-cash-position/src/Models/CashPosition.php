<?php

declare(strict_types=1);

namespace Liberu\Accounting\CashPosition\Models;

use Illuminate\Database\Eloquent\Model;

final class CashPosition extends Model
{
    protected $table = 'accounting_cash_positions';

    protected $fillable = ['team_id', 'view_ref', 'entity_ref', 'currency', 'ledger_balance', 'available_balance', 'outstanding_receipts', 'outstanding_payments', 'committed_cash', 'refreshed_at'];

    protected $casts = ['ledger_balance' => 'decimal:8', 'available_balance' => 'decimal:8', 'outstanding_receipts' => 'decimal:8', 'outstanding_payments' => 'decimal:8', 'committed_cash' => 'decimal:8', 'refreshed_at' => 'datetime'];
}
