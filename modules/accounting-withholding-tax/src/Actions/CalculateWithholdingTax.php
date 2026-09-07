<?php

declare(strict_types=1);

namespace Liberu\Accounting\WithholdingTax\Actions;

use Liberu\Accounting\WithholdingTax\Enums\WithholdingStatus;
use Liberu\Accounting\WithholdingTax\Models\WithholdingTaxDeduction;
use Liberu\Accounting\WithholdingTax\Models\WithholdingTaxRule;

final class CalculateWithholdingTax
{
    public function handle(WithholdingTaxRule $rule, array $attributes): WithholdingTaxDeduction
    {
        $gross = round((float) $attributes['gross_amount'], 2);
        $withheld = $gross <= (float) $rule->threshold ? 0.0 : round($gross * (float) $rule->rate / 100, 2);

        return WithholdingTaxDeduction::create(array_merge($attributes, ['team_id' => $rule->team_id, 'rule_id' => $rule->id, 'gross_amount' => $gross, 'withheld_amount' => $withheld, 'status' => WithholdingStatus::Calculated]));
    }
}
