<?php

declare(strict_types=1);

namespace Liberu\Accounting\BillPayments\Queries;

use Liberu\Accounting\BillPayments\Models\BillPaymentProposal;

final class BillPaymentOptimizationQuery
{
    /** @return array{recommended_date: string, discount_amount: float, rationale: string} */
    public function handle(BillPaymentProposal $payment): array
    {
        $discount = round((float) $payment->amount * ((float) $payment->discount_rate / 100), 2);
        $useDiscount = $payment->discount_date !== null && $discount > 0 && ($payment->due_date === null || $payment->discount_date->lte($payment->due_date));

        return ['recommended_date' => ($useDiscount ? $payment->discount_date : $payment->due_date)->toDateString(), 'discount_amount' => $useDiscount ? $discount : 0.0, 'rationale' => $useDiscount ? 'Pay by the discount date to capture the available discount.' : 'Pay by the contractual due date.'];
    }
}
