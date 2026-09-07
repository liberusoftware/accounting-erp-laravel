<?php

declare(strict_types=1);

namespace Liberu\Accounting\Budgets\Enums;

enum BudgetStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Revised = 'revised';
}
