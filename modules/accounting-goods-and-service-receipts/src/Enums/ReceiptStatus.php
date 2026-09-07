<?php

declare(strict_types=1);

namespace Liberu\Accounting\GoodsAndServiceReceipts\Enums;

enum ReceiptStatus: string
{
    case Draft = 'draft';
    case Received = 'received';
    case Confirmed = 'confirmed';
    case PartiallyReturned = 'partially_returned';
    case Closed = 'closed';
}
