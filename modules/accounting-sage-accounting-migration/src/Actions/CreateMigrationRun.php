<?php

declare(strict_types=1);

namespace Liberu\Accounting\SageAccountingMigration\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\SageAccountingMigration\Enums\MigrationRunStatus;
use Liberu\Accounting\SageAccountingMigration\Models\SageMigrationRun;

final class CreateMigrationRun
{
    public function handle(?int $connectionId, array $metadata = []): SageMigrationRun
    {
        return DB::transaction(fn (): SageMigrationRun => SageMigrationRun::create(['connection_id' => $connectionId, 'status' => MigrationRunStatus::Draft, 'metadata' => $metadata]));
    }
}
