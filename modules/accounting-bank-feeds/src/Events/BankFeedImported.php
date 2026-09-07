<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankFeeds\Events;

use Liberu\Accounting\BankFeeds\Models\BankFeedConnection;

final readonly class BankFeedImported
{
    public function __construct(public BankFeedConnection $connection, public int $imported, public int $duplicates) {}
}
