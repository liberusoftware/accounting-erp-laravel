<?php

declare(strict_types=1);

namespace Liberu\Accounting\FinancialStatements\Enums;

enum StatementType: string
{
    case ProfitAndLoss = 'profit_and_loss';
    case BalanceSheet = 'balance_sheet';
    case CashFlow = 'cash_flow';
    case ChangesInEquity = 'changes_in_equity';
}
