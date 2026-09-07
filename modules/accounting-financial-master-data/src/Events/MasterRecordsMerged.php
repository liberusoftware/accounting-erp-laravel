<?php

declare(strict_types=1);

namespace Liberu\Accounting\FinancialMasterData\Events;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Liberu\Accounting\FinancialMasterData\Models\Party;

final readonly class MasterRecordsMerged implements ShouldDispatchAfterCommit
{
    public function __construct(public Party $survivor, public Party $duplicate) {}
}
