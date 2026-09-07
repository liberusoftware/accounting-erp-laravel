<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollPayments\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\PayrollPayments\Enums\PaymentStatus;
use Liberu\Accounting\PayrollPayments\Exceptions\InvalidPayrollPayment;
use Liberu\Accounting\PayrollPayments\Models\PayrollPaymentBatch;

final class TransitionPayrollPayment
{
    public function handle(PayrollPaymentBatch $batch, PaymentStatus $status, ?array $details = null): PayrollPaymentBatch
    {
        $allowed = [PaymentStatus::Draft->value => [PaymentStatus::Approved, PaymentStatus::Failed], PaymentStatus::Approved->value => [PaymentStatus::Submitted, PaymentStatus::Failed], PaymentStatus::Submitted->value => [PaymentStatus::Settled, PaymentStatus::Failed], PaymentStatus::Settled->value => [PaymentStatus::Reconciled], PaymentStatus::Failed->value => [PaymentStatus::Approved]];
        if (! in_array($status, $allowed[$batch->status->value] ?? [], true)) {
            throw new InvalidPayrollPayment('Invalid payroll payment transition.');
        }$data = ['status' => $status];
        if ($status === PaymentStatus::Approved) {
            $data['approved_at'] = now();
        }if ($status === PaymentStatus::Submitted) {
            $data['submitted_at'] = now();
        }if ($status === PaymentStatus::Settled) {
            $data['settled_at'] = now();
        }if ($status === PaymentStatus::Reconciled) {
            $data['reconciled_at'] = now();
        }if ($status === PaymentStatus::Failed) {
            $data = array_merge($data, ['failure_code' => $details['failure_code'] ?? null, 'failure_message' => $details['failure_message'] ?? null]);
        }

        return DB::transaction(function () use ($batch, $data): PayrollPaymentBatch {
            $batch->update($data);

            return $batch->refresh();
        });
    }
}
