<?php

declare(strict_types=1);

namespace Liberu\Accounting\CloseManagement\Actions;

use Liberu\Accounting\CloseManagement\Enums\CloseCycleStatus;
use Liberu\Accounting\CloseManagement\Exceptions\InvalidCloseCycle;
use Liberu\Accounting\CloseManagement\Models\CloseCycle;

final class LockCloseCycle
{
    public function handle(CloseCycle $cycle): CloseCycle
    {
        if ($cycle->status !== CloseCycleStatus::Certified) {
            throw new InvalidCloseCycle('Only certified close cycles can be locked.');
        }

        $cycle->update(['status' => CloseCycleStatus::Locked]);

        return $cycle->refresh();
    }
}
