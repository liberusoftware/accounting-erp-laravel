<?php

declare(strict_types=1);

namespace Liberu\Accounting\Budgets\Actions;

use Liberu\Accounting\Budgets\Enums\BudgetStatus;
use Liberu\Accounting\Budgets\Exceptions\InvalidBudget;
use Liberu\Accounting\Budgets\Models\Budget;

final class ApproveBudget
{
    public function handle(Budget $budget, int $actorId): Budget
    {
        if ($budget->status !== BudgetStatus::Submitted) {
            throw new InvalidBudget('Only submitted budgets can be approved.');
        }
        $budget->update(['status' => BudgetStatus::Approved, 'approved_by' => $actorId, 'approved_at' => now()]);

        return $budget;
    }
}
