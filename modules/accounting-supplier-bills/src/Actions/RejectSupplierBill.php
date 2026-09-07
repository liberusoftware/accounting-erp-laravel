<?php

declare(strict_types=1);

namespace Liberu\Accounting\SupplierBills\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\SupplierBills\Enums\SupplierBillStatus;
use Liberu\Accounting\SupplierBills\Exceptions\InvalidSupplierBill;
use Liberu\Accounting\SupplierBills\Models\SupplierBill;

final class RejectSupplierBill
{
    public function handle(SupplierBill $bill, string $reason): SupplierBill
    {
        $bill = DB::transaction(function () use ($bill, $reason): SupplierBill {
            $bill = SupplierBill::query()->lockForUpdate()->findOrFail($bill->getKey());
            if (blank($reason) || ! in_array($bill->status, [SupplierBillStatus::Draft, SupplierBillStatus::Approved], true)) {
                throw new InvalidSupplierBill('Only draft or approved bills may be rejected and a reason is required.');
            }
            $bill->update(['status' => SupplierBillStatus::Rejected, 'approval_status' => 'rejected', 'rejection_reason' => $reason]);

            return $bill->refresh();
        });

        return $bill;
    }
}
