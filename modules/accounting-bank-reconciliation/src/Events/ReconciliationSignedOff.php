<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankReconciliation\Events;

use Liberu\Accounting\BankReconciliation\Models\ReconciliationSession;

final readonly class ReconciliationSignedOff
{
    public function __construct(public ReconciliationSession $session) {}
}
