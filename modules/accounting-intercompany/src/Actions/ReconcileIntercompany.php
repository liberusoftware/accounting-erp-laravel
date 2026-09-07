<?php

declare(strict_types=1);

namespace Liberu\Accounting\Intercompany\Actions;

use Liberu\Accounting\Intercompany\Exceptions\InvalidIntercompany;
use Liberu\Accounting\Intercompany\Models\IntercompanyReconciliation;

final class ReconcileIntercompany
{
    public function handle(array $a): IntercompanyReconciliation
    {
        $source = (float) ($a['source_total'] ?? -1);
        $target = (float) ($a['counterparty_total'] ?? -1);
        foreach (['reconciliation_ref', 'period_ref', 'entity_ref', 'counterparty_ref'] as $k) {
            if (blank($a[$k] ?? null)) {
                throw new InvalidIntercompany("Missing reconciliation field [{$k}].");
            }
        }if ($source < 0 || $target < 0) {
            throw new InvalidIntercompany('Reconciliation totals cannot be negative.');
        }$difference = round($source - $target, 2);

        return IntercompanyReconciliation::create(['team_id' => $a['team_id'] ?? null, 'reconciliation_ref' => $a['reconciliation_ref'], 'period_ref' => $a['period_ref'], 'entity_ref' => $a['entity_ref'], 'counterparty_ref' => $a['counterparty_ref'], 'transaction_count' => $a['transaction_count'] ?? 0, 'source_total' => $source, 'counterparty_total' => $target, 'difference_total' => $difference, 'status' => $difference === 0.0 ? 'reconciled' : 'variance', 'actor_ref' => $a['actor_ref'] ?? 'system', 'reconciled_at' => now(), 'metadata' => $a['metadata'] ?? null]);
    }
}
