<?php

declare(strict_types=1);

namespace Liberu\Accounting\BillPayments\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\BillPayments\Enums\BillPaymentStatus;
use Liberu\Accounting\BillPayments\Events\BillPaymentSubmitted;
use Liberu\Accounting\BillPayments\Exceptions\InvalidBillPayment;
use Liberu\Accounting\BillPayments\Models\BillPaymentProposal;
use Liberu\Foundation\Integrations\Contracts\PaymentProviderAdapter;

final class SubmitBillPayment
{
    public function handle(BillPaymentProposal $payment, PaymentProviderAdapter $adapter): BillPaymentProposal
    {
        return DB::transaction(function () use ($payment, $adapter): BillPaymentProposal {
            $payment = BillPaymentProposal::query()->lockForUpdate()->findOrFail($payment->getKey());
            if ($payment->status === BillPaymentStatus::Submitted || $payment->status === BillPaymentStatus::Paid) {
                return $payment;
            }
            if ($payment->status !== BillPaymentStatus::Approved) {
                throw new InvalidBillPayment('Only approved proposals can be submitted.');
            }
            try {
                $result = $adapter->sendPayment(array_merge($payment->payment_payload ?? [], ['amount' => (float) $payment->amount, 'currency' => $payment->currency, 'reference' => $payment->bill_reference, 'bank_details' => $payment->bank_details]));
                $payment->update(['status' => BillPaymentStatus::Submitted, 'submitted_at' => now(), 'provider_reference' => $result['id'] ?? $result['reference'] ?? null, 'provider_result' => $result]);
            } catch (\Throwable $exception) {
                $payment->update(['status' => BillPaymentStatus::Failed, 'failure_message' => $exception->getMessage()]);
                throw $exception;
            }
            DB::afterCommit(fn (): mixed => event(new BillPaymentSubmitted($payment->refresh())));

            return $payment->refresh();
        });
    }
}
