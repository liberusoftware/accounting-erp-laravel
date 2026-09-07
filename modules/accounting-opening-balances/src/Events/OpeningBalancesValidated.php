<?php

declare(strict_types=1);

namespace Liberu\Accounting\OpeningBalances\Events;

use Liberu\Accounting\OpeningBalances\Models\OpeningBalanceBatch;

final class OpeningBalancesValidated
{
    public function __construct(public readonly OpeningBalanceBatch $batch) {}
}
