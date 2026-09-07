<?php

declare(strict_types=1);

namespace Liberu\Accounting\Budgets\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Budgets\Enums\BudgetStatus;
use Liberu\Accounting\Budgets\Exceptions\InvalidBudget;
use Liberu\Accounting\Budgets\Models\Budget;

final class CreateBudget
{
    public function handle(array $attributes): Budget
    {
        foreach (['team_id', 'name', 'period_start', 'period_end', 'currency'] as $field) {
            if (blank($attributes[$field] ?? null)) {
                throw new InvalidBudget("{$field} is required.");
            }
        }
        if (($attributes['period_end'] ?? '') < ($attributes['period_start'] ?? '')) {
            throw new InvalidBudget('The budget period is invalid.');
        }

        return DB::transaction(fn (): Budget => Budget::create([...$attributes, 'currency' => strtoupper((string) $attributes['currency']), 'status' => BudgetStatus::Draft, 'version' => 1]));
    }
}
