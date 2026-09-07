<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectCosting\Enums;

enum CostType: string
{
    case Labor = 'labor';
    case Expense = 'expense';
    case Bill = 'bill';
    case Inventory = 'inventory';
    case Subcontractor = 'subcontractor';
    case Overhead = 'overhead';
}
