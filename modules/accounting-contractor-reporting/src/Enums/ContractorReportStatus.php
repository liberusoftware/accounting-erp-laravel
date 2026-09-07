<?php

declare(strict_types=1);

namespace Liberu\Accounting\ContractorReporting\Enums;

enum ContractorReportStatus: string
{
    case Draft = 'draft';
    case Validated = 'validated';
    case Filed = 'filed';
    case Corrected = 'corrected';
}
