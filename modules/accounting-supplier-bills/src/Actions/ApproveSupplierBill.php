<?php

declare(strict_types=1);

namespace Liberu\Accounting\SupplierBills\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\SupplierBills\Enums\SupplierBillStatus;
use Liberu\Accounting\SupplierBills\Events\SupplierBillApproved;
use Liberu\Accounting\SupplierBills\Exceptions\InvalidSupplierBill;
use Liberu\Accounting\SupplierBills\Models\SupplierBill;

final class ApproveSupplierBill
{
    public function handle(SupplierBill $bill, ?int $actorId = null): SupplierBill
    {
        return DB::transaction(function () use ($bill, $actorId): SupplierBill {
            $bill = SupplierBill::query()->lockForUpdate()->findOrFail($bill->id);
            if ($bill->status !== SupplierBillStatus::Draft || $bill->total <= 0) {
                throw new InvalidSupplierBill('Only non-empty draft supplier bills may be approved.');
            }
            $bill->update(['status' => SupplierBillStatus::Approved, 'approval_status' => 'approved', 'approved_by' => $actorId, 'approved_at' => now(), 'rejection_reason' => null]);
            $bill = $bill->refresh();
            DB::afterCommit(fn () => event(new SupplierBillApproved($bill)));

            return $bill;
        });
    }
}
