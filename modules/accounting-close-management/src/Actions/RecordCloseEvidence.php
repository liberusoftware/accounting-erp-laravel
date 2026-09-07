<?php

declare(strict_types=1);

namespace Liberu\Accounting\CloseManagement\Actions;

use Liberu\Accounting\CloseManagement\Exceptions\InvalidCloseCycle;
use Liberu\Accounting\CloseManagement\Models\CloseCycle;

final class RecordCloseEvidence
{
    public function handle(CloseCycle $cycle, array $evidence): CloseCycle
    {
        if (blank($evidence['reference'] ?? null)) {
            throw new InvalidCloseCycle('Evidence reference is required.');
        }

        $cycle->update(['evidence' => [...($cycle->evidence ?? []), $evidence]]);

        return $cycle->refresh();
    }
}
