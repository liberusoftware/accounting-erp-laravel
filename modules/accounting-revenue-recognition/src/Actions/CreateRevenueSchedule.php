<?php

declare(strict_types=1);

namespace Liberu\Accounting\RevenueRecognition\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\RevenueRecognition\Enums\RecognitionStatus;
use Liberu\Accounting\RevenueRecognition\Exceptions\InvalidRecognition;
use Liberu\Accounting\RevenueRecognition\Models\RevenueObligation;
use Liberu\Accounting\RevenueRecognition\Models\RevenueSchedule;

final class CreateRevenueSchedule
{
    public function handle(array $attributes): RevenueSchedule
    {
        $periods = (int) ($attributes['periods'] ?? 0);
        $total = round((float) ($attributes['total_amount'] ?? 0), 2);
        if ($periods < 1) {
            throw new InvalidRecognition('periods must be at least 1.');
        }if ($total < 0) {
            throw new InvalidRecognition('total_amount cannot be negative.');
        }if (blank($attributes['deferred_account_ref'] ?? null) || blank($attributes['revenue_account_ref'] ?? null) || $attributes['deferred_account_ref'] === $attributes['revenue_account_ref']) {
            throw new InvalidRecognition('Deferred and revenue account references must be different.');
        }$date = $attributes['start_date'] ?? null;
        if (blank($date)) {
            throw new InvalidRecognition('start_date is required.');
        }

        return DB::transaction(function () use ($attributes, $periods, $total, $date): RevenueSchedule {
            $obligation = RevenueObligation::create(['team_id' => $attributes['team_id'] ?? null, 'source_type' => $attributes['source_type'] ?? null, 'source_id' => $attributes['source_id'] ?? null, 'description' => $attributes['description'] ?? null, 'currency' => $attributes['currency'] ?? 'USD', 'total_amount' => $total, 'start_date' => $date, 'periods' => $periods, 'status' => RecognitionStatus::Active, 'metadata' => $attributes['metadata'] ?? null]);
            $schedule = RevenueSchedule::create(['obligation_id' => $obligation->id, 'allocation_reference_id' => $attributes['allocation_reference_id'] ?? null, 'total_amount' => $total, 'start_date' => $date, 'periods' => $periods, 'deferred_account_ref' => $attributes['deferred_account_ref'], 'revenue_account_ref' => $attributes['revenue_account_ref'], 'status' => RecognitionStatus::Active, 'funded' => (bool) ($attributes['funded'] ?? false), 'metadata' => $attributes['metadata'] ?? null]);
            $per = round($total / $periods, 2);
            for ($n = 1; $n <= $periods; $n++) {
                $amount = $n < $periods ? $per : round($total - ($per * ($periods - 1)), 2);
                $schedule->entries()->create(['period_number' => $n, 'recognition_date' => now()->parse($date)->addMonths($n - 1)->toDateString(), 'amount' => $amount, 'status' => RecognitionStatus::Active]);
            }

            return $schedule->load('entries');
        });
    }
}
