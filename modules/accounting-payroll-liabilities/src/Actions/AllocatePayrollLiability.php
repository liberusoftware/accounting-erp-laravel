<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollLiabilities\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\PayrollLiabilities\Enums\LiabilityStatus;
use Liberu\Accounting\PayrollLiabilities\Exceptions\InvalidLiability;
use Liberu\Accounting\PayrollLiabilities\Models\PayrollLiability;

final class AllocatePayrollLiability
{
    public function handle(PayrollLiability $liability, float $amount, string $allocationRef): PayrollLiability
    {
        if ($amount <= 0 || $amount > $liability->outstanding() || blank($allocationRef)) {
            throw new InvalidLiability('Allocation exceeds the outstanding liability or lacks a reference.');
        }

        return DB::transaction(function () use ($liability, $amount, $allocationRef): PayrollLiability {
            $paid = (float) $liability->paid_amount + $amount;
            $liability->update(['paid_amount' => $paid, 'payment_ref' => $allocationRef, 'allocation_ref' => $allocationRef, 'status' => $paid >= (float) $liability->amount ? LiabilityStatus::Paid : LiabilityStatus::PartPaid]);

            return $liability->refresh();
        });
    }
}
