<?php

declare(strict_types=1);

namespace Liberu\Accounting\QuickBooksOnlineMigration\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\QuickBooksOnlineMigration\Enums\MigrationStatus;
use Liberu\Accounting\QuickBooksOnlineMigration\Models\QboMigrationRun;

final class ReconcileMigration
{
    public function handle(QboMigrationRun $run): QboMigrationRun
    {
        return DB::transaction(function () use ($run): QboMigrationRun {
            $failed = $run->records()->where('status', 'failed')->count();
            $run->update(['failed_records' => $failed, 'status' => $failed === 0 ? MigrationStatus::Reconciled : MigrationStatus::Failed, 'metadata' => array_merge($run->metadata ?? [], ['reconciled_at' => now()->toIso8601String()])]);

            return $run->refresh();
        });
    }
}
