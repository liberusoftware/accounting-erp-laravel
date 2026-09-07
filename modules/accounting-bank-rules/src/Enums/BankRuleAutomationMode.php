<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankRules\Enums;

enum BankRuleAutomationMode: string
{
    case Disabled = 'disabled';
    case Suggest = 'suggest';
    case Automatic = 'automatic';
}
