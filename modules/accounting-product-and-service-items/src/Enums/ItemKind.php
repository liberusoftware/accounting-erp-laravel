<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProductAndServiceItems\Enums;

enum ItemKind: string
{
    case Item = 'item';
    case Service = 'service';
}
