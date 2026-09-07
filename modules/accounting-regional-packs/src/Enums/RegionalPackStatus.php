<?php

declare(strict_types=1);

namespace Liberu\Accounting\RegionalPacks\Enums;

enum RegionalPackStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Retired = 'retired';
}
