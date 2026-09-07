<?php

declare(strict_types=1);

namespace Liberu\Accounting\Intercompany\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Liberu\Accounting\Intercompany\Enums\TransactionStatus;
use Liberu\Accounting\Intercompany\Models\IntercompanyTransaction;

final class IntercompanyQuery
{
    public function paginate(?int $teamId = null, ?TransactionStatus $status = null, int $perPage = 25): LengthAwarePaginator
    {
        return IntercompanyTransaction::query()->when($teamId !== null, fn ($q) => $q->where('team_id', $teamId))->when($status !== null, fn ($q) => $q->where('status', $status))->with(['counterparty', 'confirmations', 'settlements', 'differences', 'evidence'])->latest('transaction_date')->paginate(min(max($perPage, 1), 100));
    }

    public function reconciliationSummary(IntercompanyTransaction $transaction): array
    {
        $confirmed = (float) $transaction->confirmations()->where('status', 'confirmed')->sum('confirmed_amount');
        $settled = (float) $transaction->settlements()->sum('amount');

        return ['transaction_amount' => (float) $transaction->amount, 'confirmed_amount' => $confirmed, 'settled_amount' => $settled, 'outstanding' => round((float) $transaction->amount - $settled, 2)];
    }
}
