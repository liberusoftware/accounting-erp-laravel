<?php

declare(strict_types=1);

namespace Liberu\Accounting\RevenueRecognition\Enums;

enum RecognitionStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Failed = 'failed';
}
