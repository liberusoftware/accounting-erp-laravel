<?php

declare(strict_types=1);

namespace Liberu\Accounting\CustomerPayments\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\CustomerPayments\Enums\CustomerPaymentStatus;
use Liberu\Accounting\CustomerPayments\Exceptions\InvalidCustomerPayment;
use Liberu\Accounting\CustomerPayments\Models\CustomerPayment;
use Liberu\Accounting\CustomerPayments\Models\CustomerPaymentAllocation;

final class AllocateCustomerPayment
{
    public function handle(CustomerPayment $payment, string $documentRef, float $amount): CustomerPaymentAllocation
    {
        if (blank($documentRef) || $amount <= 0 || $amount > (float) $payment->amount - (float) $payment->allocated_amount - (float) $payment->refunded_amount) {
            throw new InvalidCustomerPayment('Allocation exceeds the unapplied payment balance.');
        }

        return DB::transaction(function () use ($payment, $documentRef, $amount): CustomerPaymentAllocation {
            $allocation = $payment->allocations()->create(['team_id' => $payment->team_id, 'document_ref' => $documentRef, 'amount' => $amount]);
            $total = (float) $payment->allocated_amount + $amount;
            $payment->update(['allocated_amount' => $total, 'status' => $total >= (float) $payment->amount ? CustomerPaymentStatus::Allocated : CustomerPaymentStatus::PartiallyAllocated]);

            return $allocation;
        });
    }
}
