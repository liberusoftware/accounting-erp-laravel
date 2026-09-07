<?php

declare(strict_types=1);

namespace Liberu\Accounting\TaxCore\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\TaxCore\Events\TaxRuleUpdated;
use Liberu\Accounting\TaxCore\Models\TaxRule;

final class UpdateTaxRule
{
    public function handle(TaxRule $rule, array $attributes): TaxRule
    {
        return DB::transaction(function () use ($rule, $attributes): TaxRule {
            $rule->update($attributes);
            $rule = $rule->refresh();
            DB::afterCommit(fn () => event(new TaxRuleUpdated($rule)));

            return $rule;
        });
    }
}
