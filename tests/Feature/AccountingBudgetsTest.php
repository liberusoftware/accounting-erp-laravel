<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\Budgets\Actions\AddBudgetLine;
use Liberu\Accounting\Budgets\Actions\ApproveBudget;
use Liberu\Accounting\Budgets\Actions\CreateBudget;
use Liberu\Accounting\Budgets\Actions\SubmitBudget;
use Liberu\Accounting\Budgets\Enums\BudgetStatus;

uses(RefreshDatabase::class);

it('supports phased budget submission and approval', function (): void {
    $budget = app(CreateBudget::class)->handle([
        'team_id' => 101,
        'name' => 'FY2027 operating plan',
        'period_start' => '2027-01-01',
        'period_end' => '2027-12-31',
        'currency' => 'gbp',
    ]);

    app(AddBudgetLine::class)->handle($budget, [
        'account_id' => 200,
        'planned_amount' => '12000.00',
        'dimensions' => ['department' => 'operations'],
        'phases' => ['2027-01' => '1000.00'],
    ]);
    $budget = app(SubmitBudget::class)->handle($budget, 1);
    expect($budget->status)->toBe(BudgetStatus::Submitted);

    $budget = app(ApproveBudget::class)->handle($budget, 2);
    expect($budget->status)->toBe(BudgetStatus::Approved)
        ->and($budget->approved_by)->toBe(2);
});
