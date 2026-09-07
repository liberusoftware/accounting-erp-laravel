<?php

declare(strict_types=1);

namespace Liberu\Accounting\Budgets\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Budgets\Enums\BudgetStatus;
use Liberu\Accounting\Budgets\Exceptions\InvalidBudget;
use Liberu\Accounting\Budgets\Models\Budget;

final class ReviseBudget
{
    public function handle(Budget $budget, array $attributes): Budget
    {
        if ($budget->status !== BudgetStatus::Approved) {
            throw new InvalidBudget('Only approved budgets can be revised.');
        }

        return DB::transaction(function () use ($budget, $attributes): Budget {
            $budget->update([...$attributes, 'status' => BudgetStatus::Revised, 'version' => $budget->version + 1]);

            return $budget;
        });
    }
}
