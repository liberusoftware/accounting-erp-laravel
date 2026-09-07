<?php

declare(strict_types=1);

namespace Liberu\Accounting\Vat\Enums;

enum VatRecordStatus: string
{
    case Draft = 'draft';
    case Posted = 'posted';
    case Adjusted = 'adjusted';
}
