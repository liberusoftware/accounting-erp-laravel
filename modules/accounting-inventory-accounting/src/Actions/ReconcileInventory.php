<?php

declare(strict_types=1);

namespace Liberu\Accounting\InventoryAccounting\Actions;

use Liberu\Accounting\InventoryAccounting\Exceptions\InvalidInventory;
use Liberu\Accounting\InventoryAccounting\Models\InventoryItem;
use Liberu\Accounting\InventoryAccounting\Models\InventoryReconciliation;

final class ReconcileInventory
{
    public function handle(array $attributes, ?InventoryItem $item = null): InventoryReconciliation
    {
        $sub = (float) ($attributes['subledger_value'] ?? -1);
        $gl = (float) ($attributes['general_ledger_value'] ?? -1);
        if (blank($attributes['reconciliation_ref'] ?? null) || blank($attributes['period_ref'] ?? null) || $sub < 0 || $gl < 0) {
            throw new InvalidInventory('Reconciliation requires references and non-negative balances.');
        }$variance = round($sub - $gl, 2);

        return InventoryReconciliation::create(['team_id' => $attributes['team_id'] ?? null, 'reconciliation_ref' => $attributes['reconciliation_ref'], 'period_ref' => $attributes['period_ref'], 'subledger_value' => $sub, 'general_ledger_value' => $gl, 'variance' => $variance, 'status' => $variance === 0.0 ? 'reconciled' : 'variance', 'actor_ref' => $attributes['actor_ref'] ?? 'system', 'reconciled_at' => now(), 'metadata' => $attributes['metadata'] ?? null]);
    }
}
