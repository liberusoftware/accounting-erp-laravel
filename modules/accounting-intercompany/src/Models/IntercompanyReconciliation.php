<?php

declare(strict_types=1);

namespace Liberu\Accounting\Intercompany\Models;

use Illuminate\Database\Eloquent\Model;

final class IntercompanyReconciliation extends Model
{
    protected $table = 'accounting_intercompany_reconciliations';

    protected $fillable = ['team_id', 'reconciliation_ref', 'period_ref', 'entity_ref', 'counterparty_ref', 'transaction_count', 'source_total', 'counterparty_total', 'difference_total', 'status', 'actor_ref', 'reconciled_at', 'metadata'];

    protected $casts = ['transaction_count' => 'integer', 'source_total' => 'decimal:2', 'counterparty_total' => 'decimal:2', 'difference_total' => 'decimal:2', 'reconciled_at' => 'datetime', 'metadata' => 'array'];
}
