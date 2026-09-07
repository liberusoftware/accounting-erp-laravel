<?php

declare(strict_types=1);

namespace Liberu\Accounting\InventoryAccounting\Models;

use Illuminate\Database\Eloquent\Model;

final class InventoryReconciliation extends Model
{
    protected $table = 'accounting_inventory_reconciliations';

    protected $fillable = ['team_id', 'reconciliation_ref', 'period_ref', 'subledger_value', 'general_ledger_value', 'variance', 'status', 'actor_ref', 'reconciled_at', 'metadata'];

    protected $casts = ['subledger_value' => 'decimal:2', 'general_ledger_value' => 'decimal:2', 'variance' => 'decimal:2', 'reconciled_at' => 'datetime', 'metadata' => 'array'];
}
