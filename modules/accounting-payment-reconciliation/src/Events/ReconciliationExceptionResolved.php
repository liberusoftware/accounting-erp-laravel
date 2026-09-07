<?php

declare(strict_types=1);

namespace Liberu\Accounting\PaymentReconciliation\Events;

use Liberu\Accounting\PaymentReconciliation\Models\ReconciliationException;

final class ReconciliationExceptionResolved
{
    public function __construct(public readonly ReconciliationException $exception) {}
}
