<?php

declare(strict_types=1);

namespace Liberu\Accounting\WithholdingTax\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\WithholdingTax\Enums\WithholdingStatus;
use Liberu\Accounting\WithholdingTax\Exceptions\InvalidWithholdingTax;
use Liberu\Accounting\WithholdingTax\Models\WithholdingTaxRule;

final class CreateWithholdingTaxRule
{
    public function handle(array $attributes): WithholdingTaxRule
    {
        if (blank($attributes['code'] ?? null) || blank($attributes['name'] ?? null) || blank($attributes['jurisdiction'] ?? null) || blank($attributes['effective_from'] ?? null) || (float) ($attributes['rate'] ?? -1) < 0 || (float) ($attributes['rate'] ?? 0) > 100) {
            throw new InvalidWithholdingTax('Code, name, jurisdiction, effective date, and a rate between 0 and 100 are required.');
        }

        return DB::transaction(fn (): WithholdingTaxRule => WithholdingTaxRule::create(array_merge($attributes, ['status' => $attributes['status'] ?? WithholdingStatus::Draft])));
    }
}
