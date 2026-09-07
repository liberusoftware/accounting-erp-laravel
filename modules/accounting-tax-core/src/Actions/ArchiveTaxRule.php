<?php

declare(strict_types=1);

namespace Liberu\Accounting\TaxCore\Actions;

use Liberu\Accounting\TaxCore\Enums\TaxRuleStatus;
use Liberu\Accounting\TaxCore\Models\TaxRule;

final class ArchiveTaxRule
{
    public function handle(TaxRule $rule): TaxRule
    {
        $rule->update(['status' => TaxRuleStatus::Archived]);

        return $rule->refresh();
    }
}
