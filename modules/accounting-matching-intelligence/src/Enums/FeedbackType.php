<?php

declare(strict_types=1);

namespace Liberu\Accounting\MatchingIntelligence\Enums;

enum FeedbackType: string
{
    case Correct = 'correct';
    case Incorrect = 'incorrect';
    case Partial = 'partial';
}
