<?php

declare(strict_types=1);

namespace Liberu\Accounting\CodingSuggestions\Enums;

enum CodingSuggestionStatus: string
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
    case Reviewed = 'reviewed';
}
