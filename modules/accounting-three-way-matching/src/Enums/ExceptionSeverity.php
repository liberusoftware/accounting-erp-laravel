<?php

declare(strict_types=1);

namespace Liberu\Accounting\ThreeWayMatching\Enums;

enum ExceptionSeverity: string
{
    case Warning = 'warning';
    case Blocking = 'blocking';
}
