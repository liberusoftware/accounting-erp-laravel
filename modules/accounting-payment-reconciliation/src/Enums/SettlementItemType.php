<?php

declare(strict_types=1);

namespace Liberu\Accounting\PaymentReconciliation\Enums;

enum SettlementItemType: string
{
    case Charge = 'charge';
    case Fee = 'fee';
    case Refund = 'refund';
    case Dispute = 'dispute';
    case Adjustment = 'adjustment';
}
