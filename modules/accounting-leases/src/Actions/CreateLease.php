<?php

declare(strict_types=1);

namespace Liberu\Accounting\Leases\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Leases\Enums\LeaseStatus;
use Liberu\Accounting\Leases\Exceptions\InvalidLease;
use Liberu\Accounting\Leases\Models\Lease;

final class CreateLease
{
    public function handle(array $attributes): Lease
    {
        $ref = trim((string) ($attributes['lease_ref'] ?? ''));
        $payment = (float) ($attributes['payment_amount'] ?? 0);
        if ($ref === '' || blank($attributes['lessor_ref'] ?? null) || blank($attributes['commencement_date'] ?? null) || blank($attributes['end_date'] ?? null) || blank($attributes['currency'] ?? null) || $payment <= 0 || blank($attributes['useful_life_months'] ?? null)) {
            throw new InvalidLease('A lease requires lessor, dates, currency, positive payment, and useful life.');
        }if ($attributes['end_date'] < $attributes['commencement_date']) {
            throw new InvalidLease('Lease end date must not precede commencement.');
        }

        return DB::transaction(fn (): Lease => Lease::create(['team_id' => $attributes['team_id'] ?? null, 'lease_ref' => $ref, 'name' => $attributes['name'] ?? $ref, 'lessor_ref' => $attributes['lessor_ref'], 'asset_ref' => $attributes['asset_ref'] ?? null, 'commencement_date' => $attributes['commencement_date'], 'end_date' => $attributes['end_date'], 'currency' => strtoupper($attributes['currency']), 'payment_amount' => $payment, 'payment_frequency' => $attributes['payment_frequency'] ?? 'monthly', 'interest_rate' => $attributes['interest_rate'] ?? 0, 'discount_rate' => $attributes['discount_rate'] ?? 0, 'useful_life_months' => $attributes['useful_life_months'], 'status' => LeaseStatus::Draft, 'metadata' => $attributes['metadata'] ?? null]));
    }
}
