<?php

declare(strict_types=1);

namespace Liberu\Accounting\BillPayments\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\BillPayments\Enums\BillPaymentStatus;
use Liberu\Accounting\BillPayments\Events\BillPaymentApproved;
use Liberu\Accounting\BillPayments\Exceptions\InvalidBillPayment;
use Liberu\Accounting\BillPayments\Models\BillPaymentProposal;

final class ApproveBillPayment
{
    public function handle(BillPaymentProposal $payment): BillPaymentProposal
    {
        return DB::transaction(function () use ($payment): BillPaymentProposal {
            $payment = BillPaymentProposal::query()->lockForUpdate()->findOrFail($payment->getKey());
            if ($payment->status === BillPaymentStatus::Approved) {
                return $payment;
            }
            if ($payment->status !== BillPaymentStatus::PendingApproval || ($payment->requested_by !== null && $payment->requested_by === auth()->id())) {
                throw new InvalidBillPayment('A pending proposal requires a different checker for approval.');
            }
            $payment->update(['status' => BillPaymentStatus::Approved, 'approved_by' => auth()->id(), 'approved_at' => now()]);
            DB::afterCommit(fn (): mixed => event(new BillPaymentApproved($payment->refresh())));

            return $payment->refresh();
        });
    }
}
