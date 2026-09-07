<?php

declare(strict_types=1);

namespace Liberu\Accounting\Leases\Actions;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Leases\Enums\LeaseStatus;
use Liberu\Accounting\Leases\Enums\PaymentStatus;
use Liberu\Accounting\Leases\Events\LeaseActivated;
use Liberu\Accounting\Leases\Exceptions\InvalidLease;
use Liberu\Accounting\Leases\Models\Lease;

final class GenerateSchedule
{
    public function handle(Lease $lease): Lease
    {
        if ($lease->status !== LeaseStatus::Draft && $lease->status !== LeaseStatus::Modified) {
            throw new InvalidLease('Only draft or modified leases can have a schedule generated.');
        }$start = CarbonImmutable::parse($lease->commencement_date);
        $end = CarbonImmutable::parse($lease->end_date);
        $months = max(1, (int) $start->diffInMonths($end));
        $monthlyRate = (float) $lease->discount_rate / 12;
        $payment = (float) $lease->payment_amount;
        $liability = $monthlyRate > 0 ? $payment * (1 - pow(1 + $monthlyRate, -$months)) / $monthlyRate : $payment * $months;
        $depreciation = $liability / $lease->useful_life_months;

        return DB::transaction(function () use ($lease, $start, $months, $monthlyRate, $payment, $liability, $depreciation): Lease {
            $lease->payments()->delete();
            $balance = $liability;
            for ($i = 1; $i <= $months; $i++) {
                $interest = round($balance * $monthlyRate, 2);
                $principal = round($payment - $interest, 2);
                $balance = round(max(0, $balance - $principal), 2);
                $lease->payments()->create(['payment_ref' => $lease->lease_ref.'-'.$i, 'due_date' => $start->addMonths($i)->toDateString(), 'amount' => $payment, 'principal_amount' => $principal, 'interest_amount' => $interest, 'depreciation_amount' => round($depreciation, 2), 'status' => PaymentStatus::Scheduled]);
            }$lease->update(['status' => LeaseStatus::Active, 'right_of_use_asset' => $liability, 'lease_liability' => $liability, 'accumulated_depreciation' => 0]);
            $result = $lease->refresh()->load('payments');
            DB::afterCommit(fn () => event(new LeaseActivated($result)));

            return $result;
        });
    }
}
