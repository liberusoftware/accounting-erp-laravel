<?php

declare(strict_types=1);

namespace Liberu\Accounting\TaxCore\Actions;

use Liberu\Accounting\TaxCore\Enums\TaxTreatment;
use Liberu\Accounting\TaxCore\Exceptions\InvalidTaxRule;
use Liberu\Accounting\TaxCore\Models\TaxRule;

final class CalculateTax
{
    /** @return array{net:float,tax:float,gross:float,rate:float,rounding_scale:int} */
    public function handle(TaxRule $rule, float $amount): array
    {
        if ($amount < 0) {
            throw new InvalidTaxRule('Taxable amount must not be negative.');
        }
        $rate = (float) $rule->rate / 100;
        $scale = $rule->rounding_scale;
        if ($rule->treatment === TaxTreatment::Inclusive) {
            $gross = round($amount, $scale);
            $net = round($gross / (1 + $rate), $scale);
            $tax = round($gross - $net, $scale);
        } elseif (in_array($rule->treatment, [TaxTreatment::Exempt, TaxTreatment::ZeroRated], true)) {
            $net = round($amount, $scale);
            $tax = 0.0;
            $gross = $net;
        } else {
            $net = round($amount, $scale);
            $tax = round($net * $rate, $scale);
            $gross = round($net + $tax, $scale);
        }

        return ['net' => $net, 'tax' => $tax, 'gross' => $gross, 'rate' => (float) $rule->rate, 'rounding_scale' => $scale];
    }
}
