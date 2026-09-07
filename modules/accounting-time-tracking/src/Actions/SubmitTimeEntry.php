<?php

declare(strict_types=1);

namespace Liberu\Accounting\TimeTracking\Actions;

use Liberu\Accounting\TimeTracking\Enums\TimeEntryStatus;
use Liberu\Accounting\TimeTracking\Exceptions\InvalidTimeEntry;
use Liberu\Accounting\TimeTracking\Models\TimeEntry;

final class SubmitTimeEntry
{
    public function handle(TimeEntry $entry): TimeEntry
    {
        if ($entry->status !== TimeEntryStatus::Draft && $entry->status !== TimeEntryStatus::Corrected) {
            throw new InvalidTimeEntry('Only draft or corrected entries can be submitted.');
        }
        $entry->update(['status' => TimeEntryStatus::Submitted]);

        return $entry->refresh();
    }
}
