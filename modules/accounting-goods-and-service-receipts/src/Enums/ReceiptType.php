<?php

declare(strict_types=1);

namespace Liberu\Accounting\GoodsAndServiceReceipts\Enums;

enum ReceiptType: string
{
    case Goods = 'goods';
    case Service = 'service';
}
