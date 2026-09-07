<?php

declare(strict_types=1);

namespace Liberu\Accounting\MatchingIntelligence\Enums;

enum SuggestionStatus: string
{
    case Suggested = 'suggested';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
    case Automated = 'automated';
    case Expired = 'expired';
}
