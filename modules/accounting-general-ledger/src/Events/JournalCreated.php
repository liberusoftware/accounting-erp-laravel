<?php

declare(strict_types=1);

namespace Liberu\Accounting\GeneralLedger\Events;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Liberu\Accounting\GeneralLedger\Models\JournalEntry;

final class JournalCreated implements ShouldDispatchAfterCommit
{
    public function __construct(public readonly JournalEntry $journal) {}
}
