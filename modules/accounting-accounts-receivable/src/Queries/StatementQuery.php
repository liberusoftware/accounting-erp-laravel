<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsReceivable\Queries;

use Liberu\Accounting\AccountsReceivable\Models\ReceivableOpenItem;
use Liberu\Accounting\AccountsReceivable\Models\ReceivableReceipt;

final class StatementQuery
{
    public function handle(int $partyId, ?\DateTimeInterface $from = null, ?\DateTimeInterface $to = null): array
    {
        $allItems = ReceivableOpenItem::where('party_id', $partyId)->get();
        $allReceipts = ReceivableReceipt::where('party_id', $partyId)->get();
        $items = $allItems->when($from, fn ($c) => $c->where('issued_on', '>=', $from))->when($to, fn ($c) => $c->where('issued_on', '<=', $to));
        $receipts = $allReceipts->when($from, fn ($c) => $c->where('received_on', '>=', $from))->when($to, fn ($c) => $c->where('received_on', '<=', $to));
        $beforeItems = $from ? $allItems->filter(fn (ReceivableOpenItem $item): bool => $item->issued_on !== null && $item->issued_on->lt($from)) : collect();
        $beforeReceipts = $from ? $allReceipts->filter(fn (ReceivableReceipt $receipt): bool => $receipt->received_on !== null && $receipt->received_on->lt($from)) : collect();
        $opening = (float) $beforeItems->sum('original_amount') - (float) $beforeReceipts->sum('applied_amount');
        $charges = (float) $items->sum('original_amount');
        $payments = (float) $receipts->sum('applied_amount');

        return ['opening_balance' => $opening, 'charges' => $charges, 'payments' => $payments, 'closing_balance' => $opening + $charges - $payments, 'items' => $items->values(), 'receipts' => $receipts->values()];
    }
}
