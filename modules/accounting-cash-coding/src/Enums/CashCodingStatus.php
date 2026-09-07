<?php

declare(strict_types=1);

namespace Liberu\Accounting\CashCoding\Enums;

enum CashCodingStatus: string
{
    case Draft = 'draft';
    case InReview = 'in_review';
    case Posted = 'posted';
    case Undone = 'undone';
}
