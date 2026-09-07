<?php

declare(strict_types=1);

namespace Liberu\Accounting\TimeTracking\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\TimeTracking\Enums\TimeEntryStatus;
use Liberu\Accounting\TimeTracking\Exceptions\InvalidTimeEntry;
use Liberu\Accounting\TimeTracking\Models\TimeEntry;

final class CreateTimeEntry
{
    public function handle(array $attributes): TimeEntry
    {
        if (blank($attributes['worker_ref'] ?? null) || blank($attributes['worked_on'] ?? null) || (float) ($attributes['hours'] ?? 0) <= 0 || (float) ($attributes['hours'] ?? 0) > 24) {
            throw new InvalidTimeEntry('A worker, work date, and hours between 0 and 24 are required.');
        }

        return DB::transaction(fn (): TimeEntry => TimeEntry::create(array_merge($attributes, ['status' => $attributes['status'] ?? TimeEntryStatus::Draft])));
    }
}
