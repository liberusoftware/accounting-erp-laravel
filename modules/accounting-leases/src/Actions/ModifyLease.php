<?php

declare(strict_types=1);

namespace Liberu\Accounting\Leases\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Leases\Enums\LeaseStatus;
use Liberu\Accounting\Leases\Exceptions\InvalidLease;
use Liberu\Accounting\Leases\Models\Lease;

final class ModifyLease
{
    public function handle(Lease $lease, array $attributes): Lease
    {
        if ($lease->status !== LeaseStatus::Active) {
            throw new InvalidLease('Only active leases can be modified.');
        }$ref = trim((string) ($attributes['modification_ref'] ?? ''));
        if ($ref === '' || blank($attributes['effective_date'] ?? null)) {
            throw new InvalidLease('A modification requires reference and effective date.');
        }

        return DB::transaction(function () use ($lease, $attributes, $ref): Lease {
            $lease->modifications()->create(['modification_ref' => $ref, 'effective_date' => $attributes['effective_date'], 'kind' => $attributes['kind'] ?? 'remeasurement', 'old_term_end' => $lease->end_date, 'new_term_end' => $attributes['new_term_end'] ?? $lease->end_date, 'old_payment_amount' => $lease->payment_amount, 'new_payment_amount' => $attributes['new_payment_amount'] ?? $lease->payment_amount, 'adjustment_amount' => $attributes['adjustment_amount'] ?? 0, 'reason' => $attributes['reason'] ?? null]);
            $lease->update(['end_date' => $attributes['new_term_end'] ?? $lease->end_date, 'payment_amount' => $attributes['new_payment_amount'] ?? $lease->payment_amount, 'status' => LeaseStatus::Modified]);

            return $lease->refresh();
        });
    }
}
