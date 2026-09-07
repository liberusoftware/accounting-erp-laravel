<?php

declare(strict_types=1);

namespace Liberu\Accounting\WorkforceCosting\Enums;

enum WorkforceAllocationType: string
{
    case Project = 'project';
    case Department = 'department';
    case ClassDimension = 'class';
    case Location = 'location';
}
