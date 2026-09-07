<?php

declare(strict_types=1);

namespace Liberu\Accounting\Workpapers\Enums;

enum WorkpaperStatus: string
{
    case Draft = 'draft';
    case InProgress = 'in_progress';
    case Review = 'review';
    case Complete = 'complete';
}
