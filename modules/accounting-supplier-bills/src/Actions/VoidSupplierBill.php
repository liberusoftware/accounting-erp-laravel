<?php

declare(strict_types=1);

namespace Liberu\Accounting\SupplierBills\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\SupplierBills\Enums\SupplierBillStatus;
use Liberu\Accounting\SupplierBills\Exceptions\InvalidSupplierBill;
use Liberu\Accounting\SupplierBills\Models\SupplierBill;

final class VoidSupplierBill
{
    public function handle(SupplierBill $bill, string $reason): SupplierBill
    {
        $bill = DB::transaction(function () use ($bill, $reason): SupplierBill {
            $bill = SupplierBill::query()->lockForUpdate()->findOrFail($bill->getKey());
            if ($bill->status !== SupplierBillStatus::Posted || blank($reason) || $bill->amount_paid > 0) {
                throw new InvalidSupplierBill('Only unpaid posted bills may be voided and a reason is required.');
            }
            $bill->update(['status' => SupplierBillStatus::Void, 'metadata' => array_merge($bill->metadata ?? [], ['void_reason' => $reason])]);

            return $bill->refresh();
        });

        return $bill;
    }
}
