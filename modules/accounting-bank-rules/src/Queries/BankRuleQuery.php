<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankRules\Queries;

use Illuminate\Database\Eloquent\Builder;
use Liberu\Accounting\BankRules\Models\BankRule;

final class BankRuleQuery
{
    public function forTeam(int $teamId): Builder
    {
        return BankRule::query()->where('team_id', $teamId)->orderByDesc('priority')->orderBy('name');
    }

    /** @return list<BankRule> */
    public function matching(int $teamId, array $transaction): array
    {
        return $this->forTeam($teamId)->where('enabled', true)->get()->filter(fn (BankRule $rule): bool => $this->matches($rule->conditions ?? [], $transaction))->values()->all();
    }

    public function matches(array $conditions, array $transaction): bool
    {
        $text = strtolower(trim((string) ($transaction['description'] ?? $transaction['text'] ?? '')));
        $payee = strtolower(trim((string) ($transaction['payee'] ?? '')));
        $amount = (float) ($transaction['amount'] ?? 0);

        return (! isset($conditions['text']) || str_contains($text, strtolower((string) $conditions['text'])))
            && (! isset($conditions['payee']) || str_contains($payee, strtolower((string) $conditions['payee'])))
            && (! isset($conditions['amount_min']) || $amount >= (float) $conditions['amount_min'])
            && (! isset($conditions['amount_max']) || $amount <= (float) $conditions['amount_max'])
            && (! isset($conditions['account_id']) || (string) ($transaction['account_id'] ?? '') === (string) $conditions['account_id']);
    }
}
