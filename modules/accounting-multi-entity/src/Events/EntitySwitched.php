<?php

declare(strict_types=1);

namespace Liberu\Accounting\MultiEntity\Events;

use Liberu\Accounting\MultiEntity\Models\EntitySwitch;

final class EntitySwitched
{
    public function __construct(public readonly EntitySwitch $switch) {}
}
