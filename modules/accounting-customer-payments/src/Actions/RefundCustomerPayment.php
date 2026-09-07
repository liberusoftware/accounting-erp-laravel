<?php

declare(strict_types=1);

namespace Liberu\Accounting\CustomerPayments\Actions;

use Liberu\Accounting\CustomerPayments\Exceptions\InvalidCustomerPayment;
use Liberu\Accounting\CustomerPayments\Models\CustomerPayment;

final class RefundCustomerPayment
{
    public function handle(CustomerPayment $payment, float $amount): CustomerPayment
    {
        if ($amount <= 0 || $amount > (float) $payment->amount - (float) $payment->refunded_amount) {
            throw new InvalidCustomerPayment('Refund exceeds the refundable balance.');
        } $payment->increment('refunded_amount', $amount);

        return $payment->fresh('allocations');
    }
}
