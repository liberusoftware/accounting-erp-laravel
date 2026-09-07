<?php

declare(strict_types=1);

namespace Liberu\Accounting\ContractorCompliance\Enums;

enum ContractorComplianceStatus: string
{
    case Active = 'active';
    case Suspended = 'suspended';
    case Archived = 'archived';
}
