<?php

declare(strict_types=1);

namespace Liberu\Accounting\EstimatesAndQuotes\Enums;

enum EstimateStatus: string
{
    case Draft = 'draft';
    case Sent = 'sent';
    case Accepted = 'accepted';
    case Declined = 'declined';
    case Expired = 'expired';
    case Converted = 'converted';
}
