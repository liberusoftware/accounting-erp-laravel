<?php

declare(strict_types=1);

namespace Liberu\Accounting\CloseManagement\Actions;

use Liberu\Accounting\CloseManagement\Enums\CloseCycleStatus;
use Liberu\Accounting\CloseManagement\Exceptions\InvalidCloseCycle;
use Liberu\Accounting\CloseManagement\Models\CloseCycle;

final class ReopenCloseCycle
{
    public function handle(CloseCycle $cycle, string $reason): CloseCycle
    {
        if ($cycle->status !== CloseCycleStatus::Locked || blank($reason)) {
            throw new InvalidCloseCycle('Only locked cycles can be reopened with a reason.');
        }

        $cycle->update(['status' => CloseCycleStatus::Reopened, 'review' => ['reopen_reason' => $reason]]);

        return $cycle->refresh();
    }
}
