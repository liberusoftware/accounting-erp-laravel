<?php

declare(strict_types=1);

namespace Liberu\Accounting\CustomerPortal\Enums;

enum CustomerPortalRecordType: string
{
    case Estimate = 'estimate';
    case Invoice = 'invoice';
    case Credit = 'credit';
    case Statement = 'statement';
    case PaymentLink = 'payment_link';
    case Payment = 'payment';
    case Dispute = 'dispute';
    case Document = 'document';
    case Preference = 'preference';
}
