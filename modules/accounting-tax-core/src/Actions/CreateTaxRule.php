<?php

declare(strict_types=1);

namespace Liberu\Accounting\TaxCore\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\TaxCore\Enums\TaxRuleStatus;
use Liberu\Accounting\TaxCore\Enums\TaxTreatment;
use Liberu\Accounting\TaxCore\Events\TaxRuleCreated;
use Liberu\Accounting\TaxCore\Exceptions\InvalidTaxRule;
use Liberu\Accounting\TaxCore\Models\TaxRule;

final class CreateTaxRule
{
    public function handle(array $attributes): TaxRule
    {
        return DB::transaction(function () use ($attributes): TaxRule {
            $this->validate($attributes);
            if (TaxRule::query()->where('code', $attributes['code'])->where('jurisdiction_code', $attributes['jurisdiction_code'] ?? null)->where('effective_from', $attributes['effective_from'])->exists()) {
                throw new InvalidTaxRule('A tax rule already exists for this code, jurisdiction, and effective date.');
            }
            $rule = TaxRule::create(array_merge($attributes, ['status' => $attributes['status'] ?? TaxRuleStatus::Draft, 'treatment' => $attributes['treatment'] ?? TaxTreatment::Exclusive, 'rounding_method' => $attributes['rounding_method'] ?? 'half_up', 'rounding_scale' => $attributes['rounding_scale'] ?? 2]));
            DB::afterCommit(fn () => event(new TaxRuleCreated($rule)));

            return $rule;
        });
    }

    /** @param array<string,mixed> $attributes */
    private function validate(array $attributes): void
    {
        foreach (['code', 'name', 'tax_type', 'effective_from'] as $key) {
            if (blank($attributes[$key] ?? null)) {
                throw new InvalidTaxRule("Tax rule field [{$key}] is required.");
            }
        }
        if ((float) ($attributes['rate'] ?? 0) < 0 || (float) ($attributes['rate'] ?? 0) > 100) {
            throw new InvalidTaxRule('Tax rate must be between 0 and 100.');
        }
        if ((int) ($attributes['rounding_scale'] ?? 2) < 0 || (int) ($attributes['rounding_scale'] ?? 2) > 6) {
            throw new InvalidTaxRule('Rounding scale must be between 0 and 6.');
        }
        if (isset($attributes['effective_until']) && $attributes['effective_until'] < $attributes['effective_from']) {
            throw new InvalidTaxRule('Effective end must not precede effective start.');
        }
    }
}
