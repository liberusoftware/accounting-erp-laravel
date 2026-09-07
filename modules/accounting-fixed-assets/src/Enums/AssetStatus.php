<?php

declare(strict_types=1);

namespace Liberu\Accounting\FixedAssets\Enums;

enum AssetStatus: string
{
    case Draft = 'draft';
    case Acquired = 'acquired';
    case Capitalized = 'capitalized';
    case Disposed = 'disposed';
    case Archived = 'archived';
}
