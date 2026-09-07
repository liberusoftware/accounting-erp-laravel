<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsReceivable\Queries;

use Liberu\Accounting\AccountsReceivable\Models\ReceivableOpenItem;

final class AgingQuery
{
    public function handle(?int $partyId = null, ?\DateTimeInterface $asOf = null): array
    {
        $asOf = $asOf ?? now();
        $buckets = ['current' => 0.0, '1_30' => 0.0, '31_60' => 0.0, '61_90' => 0.0, 'over_90' => 0.0];
        $items = ReceivableOpenItem::query()->where('status', '!=', 'settled')->when($partyId, fn ($q, $v) => $q->where('party_id', $v))->get();
        foreach ($items as $item) {
            $days = $item->due_on ? max(0, (int) $item->due_on->diffInDays($asOf, false)) : 0;
            $key = $days <= 0 ? 'current' : ($days <= 30 ? '1_30' : ($days <= 60 ? '31_60' : ($days <= 90 ? '61_90' : 'over_90')));
            $buckets[$key] += $item->outstanding();
        }

        return $buckets;
    }
}
