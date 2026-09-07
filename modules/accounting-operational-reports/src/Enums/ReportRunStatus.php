<?php

declare(strict_types=1);

namespace Liberu\Accounting\OperationalReports\Enums;

enum ReportRunStatus: string
{
    case Draft = 'draft';
    case Running = 'running';
    case Ready = 'ready';
    case Published = 'published';
    case Failed = 'failed';
    case Archived = 'archived';
}
