<?php

declare(strict_types=1);

namespace Liberu\Accounting\CloseManagement\Actions;

use Liberu\Accounting\CloseManagement\Enums\CloseCycleStatus;
use Liberu\Accounting\CloseManagement\Exceptions\InvalidCloseCycle;
use Liberu\Accounting\CloseManagement\Models\CloseCycle;

final class CertifyCloseCycle
{
    public function handle(CloseCycle $cycle, array $certification): CloseCycle
    {
        if (blank($cycle->checklist) || blank($cycle->evidence) || blank($certification['certifier_ref'] ?? null)) {
            throw new InvalidCloseCycle('Checklist, evidence, and certifier are required.');
        }

        $cycle->update(['status' => CloseCycleStatus::Certified, 'certification' => $certification]);

        return $cycle->refresh();
    }
}
