<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsPayable\Queries;

use Liberu\Accounting\AccountsPayable\Models\PayableOpenItem;
use Liberu\Accounting\AccountsPayable\Models\PayablePayment;

final class SupplierSubledgerQuery
{
    public function handle(int $partyId): array
    {
        return [
            'open_items' => PayableOpenItem::with('disputes')->where('party_id', $partyId)->orderBy('due_on')->get(),
            'payments' => PayablePayment::with('applications')->where('party_id', $partyId)->latest('paid_on')->get(),
            'balance' => (float) PayableOpenItem::where('party_id', $partyId)->get()->sum(fn (PayableOpenItem $item): float => $item->outstanding()),
        ];
    }
}
