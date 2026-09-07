<?php

declare(strict_types=1);

namespace Liberu\Accounting\OperationalReports\Enums;

enum ReportCategory: string
{
    case ReceivablesPayables = 'receivables_payables';
    case SalesPurchases = 'sales_purchases';
    case Tax = 'tax';
    case Bank = 'bank';
    case Inventory = 'inventory';
    case Assets = 'assets';
    case Expenses = 'expenses';
    case Projects = 'projects';
    case Payroll = 'payroll';
    case Exceptions = 'exceptions';
}
