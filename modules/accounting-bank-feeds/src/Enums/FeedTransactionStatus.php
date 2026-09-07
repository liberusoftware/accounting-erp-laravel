<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankFeeds\Enums;

enum FeedTransactionStatus: string
{
    case Pending = 'pending';
    case Posted = 'posted';
    case Removed = 'removed';
}
