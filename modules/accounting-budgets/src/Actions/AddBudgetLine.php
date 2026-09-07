<?php

declare(strict_types=1);

namespace Liberu\Accounting\Budgets\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Budgets\Enums\BudgetStatus;
use Liberu\Accounting\Budgets\Exceptions\InvalidBudget;
use Liberu\Accounting\Budgets\Models\Budget;
use Liberu\Accounting\Budgets\Models\BudgetLine;

final class AddBudgetLine
{
    public function handle(Budget $budget, array $attributes): BudgetLine
    {
        if (! in_array($budget->status, [BudgetStatus::Draft, BudgetStatus::Revised], true)) {
            throw new InvalidBudget('Lines can only be added to draft or revised budgets.');
        }
        foreach (['account_id', 'planned_amount'] as $field) {
            if (! array_key_exists($field, $attributes)) {
                throw new InvalidBudget("{$field} is required.");
            }
        }

        return DB::transaction(fn (): BudgetLine => $budget->lines()->create([
            'account_id' => (int) $attributes['account_id'],
            'project_id' => isset($attributes['project_id']) ? (int) $attributes['project_id'] : null,
            'dimensions' => $attributes['dimensions'] ?? [],
            'planned_amount' => $attributes['planned_amount'],
            'phases' => $attributes['phases'] ?? [],
            'notes' => $attributes['notes'] ?? null,
        ]));
    }
}
