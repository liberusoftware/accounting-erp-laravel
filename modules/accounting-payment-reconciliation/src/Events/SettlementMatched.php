<?php

declare(strict_types=1);

namespace Liberu\Accounting\PaymentReconciliation\Events;

use Liberu\Accounting\PaymentReconciliation\Models\SettlementMatch;

final class SettlementMatched
{
    public function __construct(public readonly SettlementMatch $match) {}
}
