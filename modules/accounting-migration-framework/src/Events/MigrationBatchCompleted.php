<?php

declare(strict_types=1);

namespace Liberu\Accounting\MigrationFramework\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Liberu\Accounting\MigrationFramework\Models\MigrationBatch;

final class MigrationBatchCompleted
{
    use Dispatchable,SerializesModels;

    public function __construct(public readonly MigrationBatch $batch) {}
}
