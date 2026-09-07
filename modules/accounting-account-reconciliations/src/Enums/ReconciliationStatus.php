<?php

declare(strict_types=1);

namespace Liberu\Accounting\AccountReconciliations\Enums;

enum ReconciliationStatus: string
{
    case Draft = 'draft';
    case Prepared = 'prepared';
    case InReview = 'in_review';
    case Certified = 'certified';
    case CarriedForward = 'carried_forward';
}
