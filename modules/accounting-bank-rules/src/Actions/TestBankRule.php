<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankRules\Actions;

use Liberu\Accounting\BankRules\Models\BankRule;
use Liberu\Accounting\BankRules\Queries\BankRuleQuery;

final class TestBankRule
{
    public function __construct(private readonly BankRuleQuery $query) {}

    public function handle(BankRule $rule, array $transaction): array
    {
        $matched = $this->query->matches($rule->conditions ?? [], $transaction);

        return ['matched' => $matched, 'actions' => $matched ? ($rule->actions ?? []) : [], 'automation_mode' => $rule->automation_mode?->value];
    }
}
