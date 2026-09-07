<?php

declare(strict_types=1);

namespace Liberu\Accounting\CustomerPayments\Actions;

use Liberu\Accounting\CustomerPayments\Enums\CustomerPaymentStatus;
use Liberu\Accounting\CustomerPayments\Exceptions\InvalidCustomerPayment;
use Liberu\Accounting\CustomerPayments\Models\CustomerPayment;

final class ReconcileCustomerPayment
{
    public function handle(CustomerPayment $payment, string $depositReference): CustomerPayment
    {
        if (blank($depositReference) || $payment->status === CustomerPaymentStatus::Reconciled) {
            throw new InvalidCustomerPayment('A valid deposit reference is required and reconciled payments cannot be changed.');
        } $payment->update(['status' => CustomerPaymentStatus::Reconciled, 'deposit_reference' => $depositReference]);

        return $payment->fresh('allocations');
    }
}
