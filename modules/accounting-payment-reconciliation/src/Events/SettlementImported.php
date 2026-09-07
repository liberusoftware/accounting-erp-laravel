<?php

declare(strict_types=1);

namespace Liberu\Accounting\PaymentReconciliation\Events;

use Liberu\Accounting\PaymentReconciliation\Models\SettlementRun;

final class SettlementImported
{
    public function __construct(public readonly SettlementRun $run) {}
}
