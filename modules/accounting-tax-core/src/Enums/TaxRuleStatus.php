<?php

declare(strict_types=1);

namespace Liberu\Accounting\TaxCore\Enums;

enum TaxRuleStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Archived = 'archived';
}
