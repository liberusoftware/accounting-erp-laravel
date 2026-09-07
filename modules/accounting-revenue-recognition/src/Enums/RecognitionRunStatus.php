<?php

declare(strict_types=1);

namespace Liberu\Accounting\RevenueRecognition\Enums;

enum RecognitionRunStatus: string
{
    case Pending = 'pending';
    case Running = 'running';
    case Completed = 'completed';
    case Failed = 'failed';
    case Reconciled = 'reconciled';
}
