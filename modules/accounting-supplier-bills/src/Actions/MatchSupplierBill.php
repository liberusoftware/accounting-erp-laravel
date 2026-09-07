<?php

declare(strict_types=1);

namespace Liberu\Accounting\SupplierBills\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\SupplierBills\Enums\SupplierBillStatus;
use Liberu\Accounting\SupplierBills\Exceptions\InvalidSupplierBill;
use Liberu\Accounting\SupplierBills\Models\SupplierBill;

final class MatchSupplierBill
{
    public function handle(SupplierBill $bill, array $attributes): SupplierBill
    {
        return DB::transaction(function () use ($bill, $attributes): SupplierBill {
            if (! in_array($bill->status, [SupplierBillStatus::Approved, SupplierBillStatus::Posted], true) || blank($attributes['match_type'] ?? null) || blank($attributes['matched_type'] ?? null) || blank($attributes['matched_id'] ?? null)) {
                throw new InvalidSupplierBill('Matching requires an approved or posted bill and a complete target reference.');
            }
            $bill->matches()->firstOrCreate(['match_type' => $attributes['match_type'], 'matched_type' => $attributes['matched_type'], 'matched_id' => (string) $attributes['matched_id']], $attributes);

            return $bill->refresh()->load('matches');
        });
    }
}
