<?php

declare(strict_types=1);

namespace Liberu\Accounting\JournalApprovals\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Liberu\Accounting\JournalApprovals\Models\JournalApproval;

final class JournalApproved
{
    use Dispatchable,SerializesModels;

    public function __construct(public readonly JournalApproval $approval) {}
}
