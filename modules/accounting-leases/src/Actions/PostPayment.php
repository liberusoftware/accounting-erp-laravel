<?php

declare(strict_types=1);

namespace Liberu\Accounting\Leases\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Leases\Enums\PaymentStatus;
use Liberu\Accounting\Leases\Exceptions\InvalidLease;
use Liberu\Accounting\Leases\Models\LeasePayment;

final class PostPayment
{
    public function handle(LeasePayment $payment): LeasePayment
    {
        if ($payment->status !== PaymentStatus::Scheduled) {
            throw new InvalidLease('Only scheduled lease payments can be posted.');
        }

        return DB::transaction(function () use ($payment): LeasePayment {
            $lease = $payment->lease()->lockForUpdate()->firstOrFail();
            $lease->update(['lease_liability' => max(0, (float) $lease->lease_liability - (float) $payment->principal_amount), 'accumulated_depreciation' => (float) $lease->accumulated_depreciation + (float) $payment->depreciation_amount]);
            $payment->update(['status' => PaymentStatus::Posted, 'posted_at' => now()]);

            return $payment->refresh();
        });
    }
}
