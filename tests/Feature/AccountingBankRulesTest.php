<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\Accounting\BankRules\Actions\RecordBankRuleDecision;
use Liberu\Accounting\BankRules\Actions\SaveBankRule;
use Liberu\Accounting\BankRules\Actions\TestBankRule;
use Liberu\Accounting\BankRules\Exceptions\InvalidBankRule;
use Liberu\Accounting\BankRules\Models\BankRule;
use Liberu\Accounting\BankRules\Queries\BankRuleQuery;

uses(RefreshDatabase::class);

it('saves prioritized rules, matches transactions and records immutable decisions', function (): void {
    $rule = app(SaveBankRule::class)->handle([
        'team_id' => 71,
        'name' => 'Office supplies',
        'priority' => 20,
        'conditions' => ['text' => 'stationery', 'amount_max' => 500],
        'actions' => ['category' => 'office-supplies'],
        'automation_mode' => 'suggest',
    ]);

    $result = app(TestBankRule::class)->handle($rule, ['description' => 'Stationery order', 'amount' => 80]);
    expect($result['matched'])->toBeTrue()->and($result['actions']['category'])->toBe('office-supplies');
    expect(app(BankRuleQuery::class)->matching(71, ['description' => 'Stationery order', 'amount' => 80]))->toHaveCount(1);

    $history = app(RecordBankRuleDecision::class)->handle($rule, 'txn-1', true, 'suggested', $result['actions'], 'user-1');
    expect($history->rule_id)->toBe($rule->id)->and($rule->refresh()->histories)->toHaveCount(1);
});

it('rejects invalid split totals and amount ranges', function (): void {
    expect(fn () => app(SaveBankRule::class)->handle([
        'team_id' => 71, 'name' => 'Invalid split', 'conditions' => ['text' => 'x'], 'actions' => ['splits' => [['percentage' => 60], ['percentage' => 30]]],
    ]))->toThrow(InvalidBankRule::class);

    expect(fn () => app(SaveBankRule::class)->handle([
        'team_id' => 71, 'name' => 'Invalid range', 'conditions' => ['amount_min' => 100, 'amount_max' => 10], 'actions' => ['category' => 'other'],
    ]))->toThrow(InvalidBankRule::class);
});

it('updates a rule without duplicating its name', function (): void {
    $action = app(SaveBankRule::class);
    $rule = $action->handle(['team_id' => 71, 'name' => 'Travel', 'conditions' => ['payee' => 'air'], 'actions' => ['category' => 'travel']]);
    $updated = $action->handle(['name' => 'Travel', 'priority' => 9, 'conditions' => ['payee' => 'rail'], 'actions' => ['category' => 'travel']], $rule);

    expect($updated->id)->toBe($rule->id)->and($updated->priority)->toBe(9)->and(BankRule::query()->where('team_id', 71)->count())->toBe(1);
});
