<?php

declare(strict_types=1);

namespace Liberu\Accounting\BillPayments\Events;

use Liberu\Accounting\BillPayments\Models\BillPaymentProposal;

final readonly class BillPaymentApproved
{
    public function __construct(public BillPaymentProposal $payment) {}
}
