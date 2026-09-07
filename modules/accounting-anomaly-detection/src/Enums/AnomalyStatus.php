<?php

declare(strict_types=1);

namespace Liberu\Accounting\AnomalyDetection\Enums;

enum AnomalyStatus: string
{
    case Open = 'open';
    case Dismissed = 'dismissed';
    case SentToReview = 'sent_to_review';
    case Confirmed = 'confirmed';
}
