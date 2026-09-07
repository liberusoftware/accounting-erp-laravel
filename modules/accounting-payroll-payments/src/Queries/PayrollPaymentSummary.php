<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollPayments\Queries;

use Liberu\Accounting\PayrollPayments\Models\PayrollPaymentBatch;

final class PayrollPaymentSummary
{
    /** @return array<string,mixed> */
    public function forTeam(?int $teamId = null): array
    {
        $rows = PayrollPaymentBatch::query()->when($teamId !== null, fn ($q) => $q->where('team_id', $teamId))->get();

        return ['count' => $rows->count(), 'total_amount' => (float) $rows->sum(fn (PayrollPaymentBatch $batch): float => $batch->totalAmount()), 'pending' => $rows->whereIn('status', ['draft', 'approved', 'submitted'])->count(), 'failed' => $rows->where('status', 'failed')->count(), 'reconciled' => $rows->where('status', 'reconciled')->count()];
    }
}
