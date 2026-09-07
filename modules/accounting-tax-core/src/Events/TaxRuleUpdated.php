<?php

declare(strict_types=1);

namespace Liberu\Accounting\TaxCore\Events;

use Liberu\Accounting\TaxCore\Models\TaxRule;

final readonly class TaxRuleUpdated
{
    public function __construct(public TaxRule $taxRule) {}
}
