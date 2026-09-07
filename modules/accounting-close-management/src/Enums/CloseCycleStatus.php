<?php

declare(strict_types=1);

namespace Liberu\Accounting\CloseManagement\Enums;

enum CloseCycleStatus: string
{
    case Open = 'open';
    case Certified = 'certified';
    case Locked = 'locked';
    case Reopened = 'reopened';
}
