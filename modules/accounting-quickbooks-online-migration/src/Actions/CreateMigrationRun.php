<?php

declare(strict_types=1);

namespace Liberu\Accounting\QuickBooksOnlineMigration\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\QuickBooksOnlineMigration\Enums\MigrationStatus;
use Liberu\Accounting\QuickBooksOnlineMigration\Models\QboMigrationRun;

final class CreateMigrationRun
{
    public function handle(?int $connectionId, array $metadata = []): QboMigrationRun
    {
        return DB::transaction(fn (): QboMigrationRun => QboMigrationRun::create(['connection_id' => $connectionId, 'status' => MigrationStatus::Draft, 'metadata' => $metadata]));
    }
}
