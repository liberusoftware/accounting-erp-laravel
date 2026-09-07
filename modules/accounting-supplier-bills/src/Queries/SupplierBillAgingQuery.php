<?php

declare(strict_types=1);

namespace Liberu\Accounting\SupplierBills\Queries;

use Liberu\Accounting\SupplierBills\Models\SupplierBill;

final class SupplierBillAgingQuery
{
    public function handle(?int $partyId = null, ?\DateTimeInterface $asOf = null): array
    {
        $asOf ??= now();
        $buckets = ['current' => 0.0, '1_30' => 0.0, '31_60' => 0.0, '61_90' => 0.0, 'over_90' => 0.0];
        $bills = SupplierBill::query()->open()->when($partyId, fn ($query) => $query->where('party_id', $partyId))->get();
        foreach ($bills as $bill) {
            $days = $bill->due_on ? max(0, (int) $bill->due_on->diffInDays($asOf, false)) : 0;
            $bucket = $days <= 0 ? 'current' : ($days <= 30 ? '1_30' : ($days <= 60 ? '31_60' : ($days <= 90 ? '61_90' : 'over_90')));
            $buckets[$bucket] += $bill->outstanding();
        }

        return $buckets;
    }
}
