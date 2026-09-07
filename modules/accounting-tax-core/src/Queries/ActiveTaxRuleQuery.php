<?php

declare(strict_types=1);

namespace Liberu\Accounting\TaxCore\Queries;

use Liberu\Accounting\TaxCore\Models\TaxRule;

final class ActiveTaxRuleQuery
{
    public function handle(string $code, ?string $jurisdiction = null, ?string $on = null): ?TaxRule
    {
        return TaxRule::query()->active($on)->where('code', $code)->when($jurisdiction !== null, fn ($query) => $query->where('jurisdiction_code', $jurisdiction))->first();
    }
}
