<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Services;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Erp\Models\Account;
use Liberu\Accounting\Erp\Models\Team;

class TenantProvisioningService
{
    /** @var array<int, array{0:int,1:string,2:string}> [account_number, account_name, account_type] */
    private const CHART = [
        [1000, 'Cash', 'asset'],
        [1100, 'Accounts Receivable', 'asset'],
        [1200, 'Inventory', 'asset'],
        [1500, 'Fixed Assets', 'asset'],
        [2000, 'Accounts Payable', 'liability'],
        [2200, 'Sales Tax Payable', 'liability'],
        [2400, 'Deferred Revenue', 'liability'],
        [2600, 'Loans Payable', 'liability'],
        [3000, 'Owner Equity', 'equity'],
        [3200, 'Retained Earnings', 'equity'],
        [4000, 'Sales Revenue', 'revenue'],
        [4100, 'Other Income', 'revenue'],
        [5000, 'Cost of Goods Sold', 'expense'],
        [5100, 'Operating Expenses', 'expense'],
        [5200, 'Payroll Expense', 'expense'],
        [5300, 'Rent Expense', 'expense'],
        [5400, 'Utilities Expense', 'expense'],
        [5500, 'Depreciation Expense', 'expense'],
    ];

    public function provisionChartOfAccounts(Team $team): int
    {
        if (Account::where('team_id', $team->getKey())->exists()) {
            return 0;
        }

        return DB::transaction(function () use ($team): int {
            $count = 0;
            foreach (self::CHART as [$number, $name, $type]) {
                // team_id + user_id are NOT fillable and there is no auth() in the command/ops
                // context; set them explicitly so every account is team-scoped and owned.
                // normal_balance is intentionally left unset — Account's creating hook derives it.
                (new Account())->forceFill([
                    'account_number' => $number,
                    'account_name' => $name,
                    'account_type' => $type,
                    'team_id' => $team->getKey(),
                    'user_id' => $team->user_id,
                ])->save();
                $count++;
            }

            return $count;
        });
    }
}
