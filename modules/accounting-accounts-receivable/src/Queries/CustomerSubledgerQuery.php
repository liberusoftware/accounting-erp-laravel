<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsReceivable\Queries;

use Liberu\Accounting\AccountsReceivable\Models\ReceivableOpenItem;
use Liberu\Accounting\AccountsReceivable\Models\ReceivableReceipt;

final class CustomerSubledgerQuery
{
    public function handle(int $partyId): array
    {
        $items = ReceivableOpenItem::with('disputes')->where('party_id', $partyId)->orderBy('due_on')->get();

        return ['open_items' => $items, 'receipts' => ReceivableReceipt::with('applications')->where('party_id', $partyId)->orderByDesc('received_on')->get(), 'balance' => (float) $items->sum(fn (ReceivableOpenItem $item): float => $item->outstanding())];
    }
}
