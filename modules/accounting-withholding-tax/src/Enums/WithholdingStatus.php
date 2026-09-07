<?php

declare(strict_types=1);

namespace Liberu\Accounting\WithholdingTax\Enums;

enum WithholdingStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Calculated = 'calculated';
    case Open = 'open';
    case Submitted = 'submitted';
    case Remitted = 'remitted';
    case Filed = 'filed';
    case Cancelled = 'cancelled';
}
