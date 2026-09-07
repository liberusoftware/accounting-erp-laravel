<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountantWorkspace\Enums;

enum WorkspaceItemStatus: string
{
    case Active = 'active';
    case AtRisk = 'at_risk';
    case Waiting = 'waiting';
    case Complete = 'complete';
}
