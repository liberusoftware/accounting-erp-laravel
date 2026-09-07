<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankReconciliation\Queries;

use Liberu\Accounting\BankReconciliation\Models\ReconciliationSession;

final class ReconciliationSummaryQuery
{
    /** @return array{confirmed: int, exceptions: int, cleared_amount: float, variance: float} */
    public function handle(ReconciliationSession $session): array
    {
        $entries = $session->entries()->get();
        $cleared = (float) $entries->where('status', 'confirmed')->sum(fn ($entry): float => (float) $entry->amount);
        $exceptions = $entries->where('status', 'exception')->count();
        $variance = (float) $session->opening_balance + $cleared - (float) $session->statement_balance;

        return ['confirmed' => $entries->where('status', 'confirmed')->count(), 'exceptions' => $exceptions, 'cleared_amount' => $cleared, 'variance' => round($variance, 2)];
    }
}
