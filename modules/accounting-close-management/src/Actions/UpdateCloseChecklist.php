<?php

declare(strict_types=1);

namespace Liberu\Accounting\CloseManagement\Actions;

use Liberu\Accounting\CloseManagement\Enums\CloseCycleStatus;
use Liberu\Accounting\CloseManagement\Exceptions\InvalidCloseCycle;
use Liberu\Accounting\CloseManagement\Models\CloseCycle;

final class UpdateCloseChecklist
{
    public function handle(CloseCycle $cycle, array $checklist): CloseCycle
    {
        if ($cycle->status === CloseCycleStatus::Locked) {
            throw new InvalidCloseCycle('Locked close cycles cannot be changed.');
        }

        $cycle->update(['checklist' => $checklist]);

        return $cycle->refresh();
    }
}
