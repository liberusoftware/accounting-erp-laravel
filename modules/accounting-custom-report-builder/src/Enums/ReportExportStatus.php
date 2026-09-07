<?php

declare(strict_types=1);

namespace Liberu\Accounting\CustomReportBuilder\Enums;

enum ReportExportStatus: string
{
    case Requested = 'requested';
    case Processing = 'processing';
    case Completed = 'completed';
    case Failed = 'failed';
}
