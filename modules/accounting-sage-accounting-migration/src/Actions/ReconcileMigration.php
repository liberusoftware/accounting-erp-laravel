<?php

declare(strict_types=1);

namespace Liberu\Accounting\SageAccountingMigration\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\SageAccountingMigration\Enums\MigrationRunStatus;
use Liberu\Accounting\SageAccountingMigration\Models\SageMigrationRun;

final class ReconcileMigration
{
    public function handle(SageMigrationRun $run): SageMigrationRun
    {
        return DB::transaction(function () use ($run): SageMigrationRun {
            $failed = $run->records()->where('status', 'failed')->count();
            $run->update(['status' => $failed === 0 ? MigrationRunStatus::Reconciled : MigrationRunStatus::Failed, 'failed_records' => $failed, 'metadata' => array_merge($run->metadata ?? [], ['reconciled_at' => now()->toIso8601String()])]);

            return $run->refresh();
        });
    }
}
