<?php

declare(strict_types=1);

namespace Liberu\Accounting\BillPayments\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\BillPayments\Enums\BillPaymentStatus;
use Liberu\Accounting\BillPayments\Exceptions\InvalidBillPayment;
use Liberu\Accounting\BillPayments\Models\BillPaymentProposal;

final class RequestBillPaymentApproval
{
    public function handle(BillPaymentProposal $payment): BillPaymentProposal
    {
        if ($payment->status !== BillPaymentStatus::Draft) {
            throw new InvalidBillPayment('Only draft proposals can enter approval.');
        }

        return DB::transaction(function () use ($payment): BillPaymentProposal {
            $payment->update(['status' => BillPaymentStatus::PendingApproval, 'requested_by' => $payment->requested_by ?? auth()->id()]);

            return $payment->refresh();
        });
    }
}
