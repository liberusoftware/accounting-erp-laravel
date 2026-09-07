<?php

declare(strict_types=1);

namespace Liberu\Accounting\CustomerPayments\Enums;

enum CustomerPaymentKind: string
{
    case Receipt = 'receipt';
    case PaymentLink = 'payment_link';
    case Deposit = 'deposit';
    case Refund = 'refund';
}
