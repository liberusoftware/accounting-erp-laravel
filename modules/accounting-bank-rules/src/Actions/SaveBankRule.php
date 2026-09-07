<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankRules\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\BankRules\Enums\BankRuleAutomationMode;
use Liberu\Accounting\BankRules\Exceptions\InvalidBankRule;
use Liberu\Accounting\BankRules\Models\BankRule;

final class SaveBankRule
{
    public function handle(array $attributes, ?BankRule $rule = null): BankRule
    {
        $name = trim((string) ($attributes['name'] ?? ''));
        $teamId = (int) ($attributes['team_id'] ?? $rule?->team_id ?? 0);
        $conditions = is_array($attributes['conditions'] ?? null) ? $attributes['conditions'] : [];
        $actions = is_array($attributes['actions'] ?? null) ? $attributes['actions'] : [];
        $mode = $attributes['automation_mode'] ?? BankRuleAutomationMode::Suggest->value;
        $mode = $mode instanceof BankRuleAutomationMode ? $mode->value : $mode;

        if ($teamId < 1 || $name === '' || ! in_array($mode, array_column(BankRuleAutomationMode::cases(), 'value'), true) || $conditions === [] || $actions === []) {
            throw new InvalidBankRule('A bank rule requires a team, name, condition, action, and valid automation mode.');
        }
        if (isset($conditions['amount_min'], $conditions['amount_max']) && (float) $conditions['amount_min'] > (float) $conditions['amount_max']) {
            throw new InvalidBankRule('The minimum amount cannot exceed the maximum amount.');
        }
        if (isset($actions['splits'])) {
            $total = array_sum(array_map(fn (mixed $split): float => (float) (is_array($split) ? ($split['percentage'] ?? 0) : 0), $actions['splits']));
            if (abs($total - 100) > 0.0001) {
                throw new InvalidBankRule('Split action percentages must total 100.');
            }
        }

        return DB::transaction(function () use ($attributes, $rule, $teamId, $name, $conditions, $actions, $mode): BankRule {
            $duplicate = BankRule::query()->where('team_id', $teamId)->where('name', $name)->when($rule !== null, fn ($query): mixed => $query->where($query->getModel()->getKeyName(), '!=', $rule->getKey()))->exists();
            if ($duplicate) {
                throw new InvalidBankRule('A bank rule with this name already exists.');
            }

            $values = array_merge($attributes, ['team_id' => $teamId, 'name' => $name, 'priority' => max(0, (int) ($attributes['priority'] ?? 0)), 'conditions' => $conditions, 'actions' => $actions, 'automation_mode' => $mode, 'enabled' => (bool) ($attributes['enabled'] ?? true)]);

            if ($rule === null) {
                return BankRule::query()->create($values)->refresh();
            }

            $rule->update($values);

            return $rule->refresh();
        });
    }
}
