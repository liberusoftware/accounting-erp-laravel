<?php

declare(strict_types=1);

namespace Liberu\Accounting\SageAccountingMigration\Actions;

use Illuminate\Support\Facades\DB;
use Liberu\Accounting\SageAccountingMigration\Enums\MigrationRecordStatus;
use Liberu\Accounting\SageAccountingMigration\Enums\MigrationRunStatus;
use Liberu\Accounting\SageAccountingMigration\Exceptions\InvalidMigration;
use Liberu\Accounting\SageAccountingMigration\Models\SageMigrationRecord;
use Liberu\Accounting\SageAccountingMigration\Models\SageMigrationRun;

final class ImportMigrationRecords
{
    public function handle(SageMigrationRun $run, array $records): SageMigrationRun
    {
        return DB::transaction(function () use ($run, $records): SageMigrationRun {
            $run->update(['status' => MigrationRunStatus::Running, 'started_at' => now(), 'total_records' => count($records)]);
            foreach ($records as $record) {
                if (! is_array($record) || blank($record['entity_type'] ?? null) || blank($record['source_id'] ?? null) || ! is_array($record['payload'] ?? null)) {
                    throw new InvalidMigration('Each migration record requires entity_type, source_id, and payload.');
                }$payload = $record['payload'];
                SageMigrationRecord::updateOrCreate(['run_id' => $run->id, 'entity_type' => $record['entity_type'], 'source_id' => (string) $record['source_id']], ['status' => MigrationRecordStatus::Imported, 'payload' => $payload, 'payload_hash' => hash('sha256', json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES)), 'metadata' => $record['metadata'] ?? null]);
            }$run->update(['status' => MigrationRunStatus::Completed, 'imported_records' => count($records), 'finished_at' => now()]);

            return $run->refresh();
        });
    }
}
