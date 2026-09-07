<?php

declare(strict_types=1);

namespace Liberu\Accounting\BillPayments\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\BillPayments\Enums\BillPaymentStatus;
use Liberu\Accounting\BillPayments\Exceptions\InvalidBillPayment;
use Liberu\Accounting\BillPayments\Models\BillPaymentProposal;

final class MarkBillPaymentPaid
{
    public function handle(BillPaymentProposal $payment): BillPaymentProposal
    {
        if ($payment->status !== BillPaymentStatus::Submitted) {
            throw new InvalidBillPayment('Only submitted payments can be marked paid.');
        }

        return DB::transaction(function () use ($payment): BillPaymentProposal {
            $payment->update(['status' => BillPaymentStatus::Paid, 'paid_at' => now()]);

            return $payment->refresh();
        });
    }
}
