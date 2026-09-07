<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountsPayable\Queries;

use Liberu\Accounting\AccountsPayable\Models\PayableAccount;

final class ControlAccountReconciliationQuery
{
    public function handle(): array
    {
        $accounts = PayableAccount::query()->with('openItems')->get();
        $subledger = (float) $accounts->sum('current_balance');

        return ['control_account_code' => 'accounts_payable', 'subledger_balance' => $subledger, 'control_balance' => null, 'difference' => null, 'accounts' => $accounts];
    }
}
