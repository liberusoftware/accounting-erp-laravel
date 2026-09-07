<?php

declare(strict_types=1);

namespace Liberu\Accounting\BillPayments\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\BillPayments\Enums\BillPaymentStatus;
use Liberu\Accounting\BillPayments\Exceptions\InvalidBillPayment;
use Liberu\Accounting\BillPayments\Models\BillPaymentProposal;

final class RejectBillPayment
{
    public function handle(BillPaymentProposal $payment, string $reason): BillPaymentProposal
    {
        if ($payment->status !== BillPaymentStatus::PendingApproval || blank($reason)) {
            throw new InvalidBillPayment('Only pending proposals can be rejected and a reason is required.');
        }

        return DB::transaction(function () use ($payment, $reason): BillPaymentProposal {
            $payment->update(['status' => BillPaymentStatus::Rejected, 'rejected_at' => now(), 'failure_message' => $reason]);

            return $payment->refresh();
        });
    }
}
