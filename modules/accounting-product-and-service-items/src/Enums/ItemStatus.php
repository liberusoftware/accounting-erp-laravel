<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProductAndServiceItems\Enums;

enum ItemStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Archived = 'archived';
}
