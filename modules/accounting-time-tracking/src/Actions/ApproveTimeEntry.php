<?php

declare(strict_types=1);

namespace Liberu\Accounting\TimeTracking\Actions;

use Liberu\Accounting\TimeTracking\Enums\TimeEntryStatus;
use Liberu\Accounting\TimeTracking\Exceptions\InvalidTimeEntry;
use Liberu\Accounting\TimeTracking\Models\TimeEntry;

final class ApproveTimeEntry
{
    public function handle(TimeEntry $entry): TimeEntry
    {
        if ($entry->status !== TimeEntryStatus::Submitted) {
            throw new InvalidTimeEntry('Only submitted entries can be approved.');
        }
        $entry->update(['status' => TimeEntryStatus::Approved]);

        return $entry->refresh();
    }
}
