<?php

declare(strict_types=1);

namespace Liberu\Accounting\MultiCurrency\Events;

use Liberu\Accounting\MultiCurrency\Models\RevaluationRun;

final class RevaluationCalculated
{
    public function __construct(public readonly RevaluationRun $run) {}
}
