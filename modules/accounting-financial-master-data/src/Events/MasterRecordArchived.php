<?php

declare(strict_types=1);

namespace Liberu\Accounting\FinancialMasterData\Events;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;

final readonly class MasterRecordArchived implements ShouldDispatchAfterCommit
{
    use Dispatchable;

    public function __construct(public Model $record) {}
}
