<?php

declare(strict_types=1);

namespace Liberu\Accounting\RevenueRecognition\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\RevenueRecognition\Enums\RecognitionStatus;
use Liberu\Accounting\RevenueRecognition\Exceptions\InvalidRecognition;
use Liberu\Accounting\RevenueRecognition\Models\RevenueModification;
use Liberu\Accounting\RevenueRecognition\Models\RevenueSchedule;

final class ModifyRevenueSchedule
{
    public function handle(RevenueSchedule $schedule, array $attributes): RevenueModification
    {
        if ($schedule->status === RecognitionStatus::Completed || $schedule->status === RecognitionStatus::Cancelled) {
            throw new InvalidRecognition('A completed or cancelled schedule cannot be modified.');
        }if (blank($attributes['reason'] ?? null)) {
            throw new InvalidRecognition('A modification reason is required.');
        }

        return DB::transaction(fn (): RevenueModification => RevenueModification::create(['schedule_id' => $schedule->id, 'effective_date' => $attributes['effective_date'] ?? now()->toDateString(), 'amount_delta' => $attributes['amount_delta'] ?? 0, 'reason' => $attributes['reason'], 'status' => 'approved', 'metadata' => $attributes['metadata'] ?? null]));
    }
}
