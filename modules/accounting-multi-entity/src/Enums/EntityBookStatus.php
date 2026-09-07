<?php

declare(strict_types=1);

namespace Liberu\Accounting\MultiEntity\Enums;

enum EntityBookStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Suspended = 'suspended';
    case Archived = 'archived';
}
