<?php

declare(strict_types=1);

namespace Liberu\Accounting\Policies\Actions;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\DB;
use Liberu\Accounting\Policies\Enums\PolicyCategory;
use Liberu\Accounting\Policies\Events\PolicyRuleSaved;
use Liberu\Accounting\Policies\Exceptions\InvalidPolicyRule;
use Liberu\Accounting\Policies\Models\PolicyRule;

final class SavePolicyRule
{
    public function __construct(private readonly Dispatcher $events) {}

    /** @param array<string,mixed> $attributes */
    public function handle(?PolicyRule $rule, array $attributes): PolicyRule
    {
        foreach (['book_id', 'category', 'key', 'value', 'effective_from'] as $required) {
            if (! array_key_exists($required, $attributes) || ($required !== 'value' && blank($attributes[$required]))) {
                throw new InvalidPolicyRule("Policy rule {$required} is required.");
            }
        }
        if (! is_array($attributes['value'])) {
            throw new InvalidPolicyRule('Policy rule value must be an array.');
        }
        $attributes['category'] = $attributes['category'] instanceof PolicyCategory ? $attributes['category']->value : (string) $attributes['category'];
        if (! PolicyCategory::tryFrom($attributes['category'])) {
            throw new InvalidPolicyRule('Policy rule category is not supported.');
        }
        if (($attributes['effective_until'] ?? null) !== null && $attributes['effective_from'] > $attributes['effective_until']) {
            throw new InvalidPolicyRule('Policy effective end must not precede its start.');
        }

        return DB::transaction(function () use ($rule, $attributes): PolicyRule {
            $query = PolicyRule::query()->where('book_id', $attributes['book_id'])->where('category', $attributes['category'])->where('key', $attributes['key'])->where('effective_from', '<=', $attributes['effective_until'] ?? '9999-12-31')->where(function ($q) use ($attributes) {
                $q->whereNull('effective_until')->orWhere('effective_until', '>=', $attributes['effective_from']);
            });
            if ($rule) {
                $query->where('id', '!=', $rule->getKey());
            }
            if ($query->exists()) {
                throw new InvalidPolicyRule('Effective-dated policy rules may not overlap for the same book, category, and key.');
            }
            $rule ??= new PolicyRule();
            $rule->fill($attributes);
            $rule->save();
            $saved = $rule->refresh();
            DB::afterCommit(fn () => $this->events->dispatch(new PolicyRuleSaved($saved)));

            return $saved;
        });
    }
}
