<?php

declare(strict_types=1);

namespace Liberu\Accounting\Policies\Enums;

enum PolicyCategory: string
{
    case Recognition = 'recognition';
    case Capitalization = 'capitalization';
    case Depreciation = 'depreciation';
    case Fx = 'fx';
    case Tax = 'tax';
    case Rounding = 'rounding';
    case WriteOff = 'write_off';
    case Materiality = 'materiality';
    case Approval = 'approval';
}
