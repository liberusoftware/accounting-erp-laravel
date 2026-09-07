<?php

declare(strict_types=1);

namespace Liberu\Accounting\Budgets\Actions;

use Liberu\Accounting\Budgets\Enums\BudgetStatus;
use Liberu\Accounting\Budgets\Exceptions\InvalidBudget;
use Liberu\Accounting\Budgets\Models\Budget;

final class SubmitBudget
{
    public function handle(Budget $budget, int $actorId): Budget
    {
        if ($budget->status !== BudgetStatus::Draft && $budget->status !== BudgetStatus::Revised || $budget->lines()->count() === 0) {
            throw new InvalidBudget('Only a draft or revised budget with lines can be submitted.');
        }
        $budget->update(['status' => BudgetStatus::Submitted, 'submitted_by' => $actorId]);

        return $budget;
    }
}
