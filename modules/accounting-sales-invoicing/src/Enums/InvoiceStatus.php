<?php

declare(strict_types=1);

namespace Liberu\Accounting\SalesInvoicing\Enums;

enum InvoiceStatus: string
{
    case Draft = 'draft';
    case Approved = 'approved';
    case Final = 'final';
}
