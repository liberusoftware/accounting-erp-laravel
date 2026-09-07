<?php

declare(strict_types=1);

namespace Liberu\Accounting\Reimbursements\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Reimbursements\Enums\BatchStatus;
use Liberu\Accounting\Reimbursements\Enums\ReimbursementStatus;
use Liberu\Accounting\Reimbursements\Exceptions\InvalidReimbursement;
use Liberu\Accounting\Reimbursements\Models\ReimbursementBatch;

final class UpdatePaymentProviderStatus
{
    public function handle(ReimbursementBatch $batch, array $attributes): ReimbursementBatch
    {
        $status = BatchStatus::tryFrom((string) ($attributes['status'] ?? ''));
        if ($status === null) {
            throw new InvalidReimbursement('Unknown provider status.');
        }

        return DB::transaction(function () use ($batch, $status, $attributes): ReimbursementBatch {
            $batch->update(['status' => $status, 'provider' => $attributes['provider'] ?? $batch->provider, 'provider_ref' => $attributes['provider_ref'] ?? $batch->provider_ref, 'failure_message' => $status === BatchStatus::Failed ? ($attributes['failure_message'] ?? 'Provider reported failure.') : null, 'submitted_at' => $status === BatchStatus::Submitted ? now() : $batch->submitted_at, 'paid_at' => $status === BatchStatus::Paid ? now() : $batch->paid_at]);
            $batch->liabilities()->update(['status' => match ($status) {
                BatchStatus::Paid => ReimbursementStatus::Paid,BatchStatus::Failed => ReimbursementStatus::Failed,BatchStatus::Reconciled => ReimbursementStatus::Reconciled,default => ReimbursementStatus::Submitted
            }]);

            return $batch->refresh();
        });
    }
}
