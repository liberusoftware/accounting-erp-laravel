<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollPayments\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\PayrollPayments\Enums\PaymentStatus;
use Liberu\Accounting\PayrollPayments\Exceptions\InvalidPayrollPayment;
use Liberu\Accounting\PayrollPayments\Models\PayrollPaymentBatch;

final class CreatePayrollPaymentBatch
{
    /** @param array<string,mixed> $attributes */
    public function handle(array $attributes): PayrollPaymentBatch
    {
        $net = (float) ($attributes['net_pay_amount'] ?? 0);
        $liability = (float) ($attributes['liability_amount'] ?? 0);
        if (blank($attributes['batch_ref'] ?? null) || ($net < 0) || ($liability < 0) || ($net + $liability <= 0)) {
            throw new InvalidPayrollPayment('Batch reference and positive non-negative amounts are required.');
        }

        return DB::transaction(function () use ($attributes, $net, $liability): PayrollPaymentBatch {
            $batch = PayrollPaymentBatch::query()->firstOrNew(['team_id' => $attributes['team_id'] ?? null, 'batch_ref' => $attributes['batch_ref']]);
            $batch->fill(array_merge($attributes, ['net_pay_amount' => $net, 'liability_amount' => $liability, 'status' => $attributes['status'] ?? PaymentStatus::Draft]));
            $batch->save();

            return $batch;
        });
    }
}
