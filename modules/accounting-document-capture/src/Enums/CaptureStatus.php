<?php

declare(strict_types=1);

namespace Liberu\Accounting\DocumentCapture\Enums;

enum CaptureStatus: string
{
    case Uploaded = 'uploaded';
    case Extracted = 'extracted';
    case InReview = 'in_review';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Archived = 'archived';
}
