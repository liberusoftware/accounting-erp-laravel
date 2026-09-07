<?php

declare(strict_types=1);

namespace Liberu\Accounting\Dimensions\Enums;

enum DimensionKind: string
{
    case ClassCategory = 'class';
    case Location = 'location';
    case Department = 'department';
    case CostCenter = 'cost_center';
    case ProfitCenter = 'profit_center';
    case Project = 'project';
    case Tag = 'tag';
}
