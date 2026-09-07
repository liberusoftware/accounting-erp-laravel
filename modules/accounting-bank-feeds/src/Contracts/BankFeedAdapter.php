<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankFeeds\Contracts;

use Liberu\Accounting\BankFeeds\Models\BankFeedConnection;

interface BankFeedAdapter
{
    /** @return array{added: list<array<string, mixed>>, modified: list<array<string, mixed>>, removed: list<string>, next_cursor: string|null} */
    public function fetch(BankFeedConnection $connection): array;
}
