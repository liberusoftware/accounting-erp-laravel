<?php

declare(strict_types=1);

namespace Liberu\Accounting\ManagementReporting\Enums;

enum ReportStatus: string
{
    case Draft = 'draft';
    case InReview = 'in_review';
    case Approved = 'approved';
    case Delivered = 'delivered';
    case Archived = 'archived';
    case Failed = 'failed';
}
