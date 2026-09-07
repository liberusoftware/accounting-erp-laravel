<?php

declare(strict_types=1);

namespace Liberu\Accounting\SupplierBills\Queries;

use Illuminate\Support\Collection;
use Liberu\Accounting\SupplierBills\Models\SupplierBill;

final class DuplicateSupplierBillQuery
{
    public function handle(int $partyId, string $billNumber, ?int $exceptId = null): Collection
    {
        return SupplierBill::query()->where('party_id', $partyId)->where('bill_number', $billNumber)->when($exceptId, fn ($query) => $query->whereKeyNot($exceptId))->with('party')->get();
    }
}
