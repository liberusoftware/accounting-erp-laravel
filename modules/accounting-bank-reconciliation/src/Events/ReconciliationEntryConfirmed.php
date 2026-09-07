<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankReconciliation\Events;

use Liberu\Accounting\BankReconciliation\Models\ReconciliationEntry;

final readonly class ReconciliationEntryConfirmed
{
    public function __construct(public ReconciliationEntry $entry) {}
}
